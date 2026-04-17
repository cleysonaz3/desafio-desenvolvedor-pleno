const state = {
  token: localStorage.getItem('catalogo.token') || null,
  user: JSON.parse(localStorage.getItem('catalogo.user') || 'null'),
  categories: [],
  products: [],
  productsMeta: null,
  currentPage: 1,
};

const els = {
  backendStatusBadge: document.getElementById('backendStatusBadge'),
  backendStatusMessage: document.getElementById('backendStatusMessage'),
  backendVersion: document.getElementById('backendVersion'),
  backendAppHealth: document.getElementById('backendAppHealth'),
  backendDatabaseHealth: document.getElementById('backendDatabaseHealth'),
  authStateBadge: document.getElementById('authStateBadge'),
  currentUserName: document.getElementById('currentUserName'),
  currentUserEmail: document.getElementById('currentUserEmail'),
  registerForm: document.getElementById('registerForm'),
  loginForm: document.getElementById('loginForm'),
  logoutButton: document.getElementById('logoutButton'),
  categoryForm: document.getElementById('categoryForm'),
  resetCategoryFormButton: document.getElementById('resetCategoryFormButton'),
  categoryFormTitle: document.getElementById('categoryFormTitle'),
  categoriesTableBody: document.getElementById('categoriesTableBody'),
  refreshCategoriesButton: document.getElementById('refreshCategoriesButton'),
  productForm: document.getElementById('productForm'),
  resetProductFormButton: document.getElementById('resetProductFormButton'),
  productFormTitle: document.getElementById('productFormTitle'),
  productCategory: document.getElementById('productCategory'),
  filterCategory: document.getElementById('filterCategory'),
  productFiltersForm: document.getElementById('productFiltersForm'),
  resetFiltersButton: document.getElementById('resetFiltersButton'),
  refreshProductsButton: document.getElementById('refreshProductsButton'),
  productsGrid: document.getElementById('productsGrid'),
  productsCountLabel: document.getElementById('productsCountLabel'),
  productsPageLabel: document.getElementById('productsPageLabel'),
  previousPageButton: document.getElementById('previousPageButton'),
  nextPageButton: document.getElementById('nextPageButton'),
  appToast: document.getElementById('appToast'),
  appToastMessage: document.getElementById('appToastMessage'),
};

const toast = new bootstrap.Toast(els.appToast);

function getApiPath(path) {
  return path;
}

async function request(path, options = {}) {
  const headers = new Headers(options.headers || {});
  headers.set('Accept', 'application/json');

  if (options.body && !headers.has('Content-Type')) {
    headers.set('Content-Type', 'application/json');
  }

  if (state.token) {
    headers.set('Authorization', `Bearer ${state.token}`);
  }

  const response = await fetch(getApiPath(path), {
    ...options,
    headers,
    body: options.body ? JSON.stringify(options.body) : undefined,
  });

  if (response.status === 204) {
    return null;
  }

  const data = await response.json();

  if (!response.ok) {
    const message = data.message || 'Erro na requisição.';
    const details = data.errors ? ` ${Object.values(data.errors).flat().join(' ')}` : '';
    throw new Error(`${message}${details}`);
  }

  return data;
}

function notify(message) {
  els.appToastMessage.textContent = message;
  toast.show();
}

function setAuthState(token, user) {
  state.token = token;
  state.user = user;

  if (token) {
    localStorage.setItem('catalogo.token', token);
  } else {
    localStorage.removeItem('catalogo.token');
  }

  if (user) {
    localStorage.setItem('catalogo.user', JSON.stringify(user));
  } else {
    localStorage.removeItem('catalogo.user');
  }

  syncAuthView();
}

function syncAuthView() {
  const connected = Boolean(state.token && state.user);
  els.authStateBadge.textContent = connected ? 'autenticado' : 'desconectado';
  els.authStateBadge.className = `badge ${connected ? 'text-bg-success' : 'text-bg-dark'}`;
  els.currentUserName.textContent = state.user?.name || '-';
  els.currentUserEmail.textContent = state.user?.email || '-';
}

async function loadStatus() {
  try {
    const data = await request('/backend-status');
    const status = data.status || 'desconhecido';
    const databaseStatus = data.checks?.database?.status || '-';
    const appStatus = data.checks?.application?.status || '-';

    els.backendStatusBadge.textContent = status;
    els.backendStatusBadge.className = `badge ${status === 'ok' ? 'badge-health-ok' : 'badge-health-degraded'}`;
    els.backendStatusMessage.textContent = data.checks?.database?.message || 'Status consultado.';
    els.backendVersion.textContent = data.version || '-';
    els.backendAppHealth.textContent = appStatus;
    els.backendDatabaseHealth.textContent = databaseStatus;
  } catch (error) {
    els.backendStatusBadge.textContent = 'erro';
    els.backendStatusBadge.className = 'badge badge-health-error';
    els.backendStatusMessage.textContent = error.message;
    els.backendVersion.textContent = '-';
    els.backendAppHealth.textContent = 'error';
    els.backendDatabaseHealth.textContent = 'error';
  }
}

async function registerUser(event) {
  event.preventDefault();
  const formData = new FormData(event.currentTarget);

  try {
    const data = await request('/api/register', {
      method: 'POST',
      body: Object.fromEntries(formData.entries()),
    });

    setAuthState(data.data.token, data.data.user);
    notify('Usuário cadastrado e autenticado com sucesso.');
    event.currentTarget.reset();
    await refreshProtectedData();
  } catch (error) {
    notify(error.message);
  }
}

async function loginUser(event) {
  event.preventDefault();
  const formData = new FormData(event.currentTarget);

  try {
    const data = await request('/api/login', {
      method: 'POST',
      body: Object.fromEntries(formData.entries()),
    });

    setAuthState(data.data.token, data.data.user);
    notify('Login realizado com sucesso.');
    await refreshProtectedData();
  } catch (error) {
    notify(error.message);
  }
}

async function logoutUser() {
  if (!state.token) {
    notify('Nenhum usuário autenticado.');
    return;
  }

  try {
    await request('/api/logout', { method: 'POST' });
  } catch (error) {
    notify(error.message);
  } finally {
    setAuthState(null, null);
    resetCategoryForm();
    resetProductForm();
    renderCategories();
    renderProducts();
  }
}

async function loadCategories() {
  if (!state.token) {
    state.categories = [];
    renderCategories();
    populateCategorySelects();
    return;
  }

  try {
    const data = await request('/api/categories');
    state.categories = data.data ?? [];
    renderCategories();
    populateCategorySelects();
  } catch (error) {
    notify(error.message);
  }
}

function renderCategories() {
  if (!state.token) {
    els.categoriesTableBody.innerHTML = '<tr><td colspan="3" class="text-center text-body-secondary py-4">Faça login para carregar as categorias.</td></tr>';
    return;
  }

  if (!state.categories.length) {
    els.categoriesTableBody.innerHTML = '<tr><td colspan="3" class="text-center text-body-secondary py-4">Nenhuma categoria cadastrada.</td></tr>';
    return;
  }

  els.categoriesTableBody.innerHTML = state.categories.map((category) => `
    <tr>
      <td>
        <strong>${escapeHtml(category.name)}</strong>
        <div class="text-body-secondary small">${escapeHtml(category.description || 'Sem descrição')}</div>
      </td>
      <td>${category.products_count ?? 0}</td>
      <td class="text-end">
        <div class="d-flex justify-content-end gap-2">
          <button class="btn btn-sm btn-outline-dark" type="button" data-action="edit-category" data-id="${category.id}">Editar</button>
          <button class="btn btn-sm btn-outline-danger" type="button" data-action="delete-category" data-id="${category.id}">Excluir</button>
        </div>
      </td>
    </tr>
  `).join('');
}

function populateCategorySelects() {
  const options = ['<option value="">Selecione</option>']
    .concat(state.categories.map((category) => `<option value="${category.id}">${escapeHtml(category.name)}</option>`))
    .join('');

  els.productCategory.innerHTML = options;
  els.filterCategory.innerHTML = '<option value="">Todas</option>' +
    state.categories.map((category) => `<option value="${category.id}">${escapeHtml(category.name)}</option>`).join('');
}

async function saveCategory(event) {
  event.preventDefault();

  if (!state.token) {
    notify('Faça login para salvar categorias.');
    return;
  }

  const formData = new FormData(event.currentTarget);
  const id = formData.get('id');
  const payload = {
    name: formData.get('name'),
    description: formData.get('description') || null,
  };

  try {
    if (id) {
      await request(`/api/categories/${id}`, { method: 'PUT', body: payload });
      notify('Categoria atualizada com sucesso.');
    } else {
      await request('/api/categories', { method: 'POST', body: payload });
      notify('Categoria criada com sucesso.');
    }

    resetCategoryForm();
    await loadCategories();
    await loadProducts();
  } catch (error) {
    notify(error.message);
  }
}

function resetCategoryForm() {
  els.categoryForm.reset();
  document.getElementById('categoryId').value = '';
  els.categoryFormTitle.textContent = 'Nova categoria';
}

function editCategory(id) {
  const category = state.categories.find((item) => String(item.id) === String(id));

  if (!category) {
    return;
  }

  document.getElementById('categoryId').value = category.id;
  document.getElementById('categoryName').value = category.name;
  document.getElementById('categoryDescription').value = category.description || '';
  els.categoryFormTitle.textContent = `Editar: ${category.name}`;
  window.scrollTo({ top: document.getElementById('categoriesSection').offsetTop - 20, behavior: 'smooth' });
}

async function deleteCategory(id) {
  if (!confirm('Excluir esta categoria?')) {
    return;
  }

  try {
    await request(`/api/categories/${id}`, { method: 'DELETE' });
    notify('Categoria excluída.');
    resetCategoryForm();
    await loadCategories();
    await loadProducts();
  } catch (error) {
    notify(error.message);
  }
}

function getProductFilterQuery(page = 1) {
  const formData = new FormData(els.productFiltersForm);
  const params = new URLSearchParams();

  for (const [key, value] of formData.entries()) {
    if (value !== '') {
      params.set(key, value);
    }
  }

  params.set('page', String(page));

  return params.toString();
}

async function loadProducts(page = 1) {
  state.currentPage = page;

  if (!state.token) {
    state.products = [];
    state.productsMeta = null;
    renderProducts();
    return;
  }

  try {
    const data = await request(`/api/products?${getProductFilterQuery(page)}`);
    state.products = data.data ?? [];
    state.productsMeta = data.meta ?? null;
    renderProducts();
  } catch (error) {
    notify(error.message);
  }
}

function renderProducts() {
  if (!state.token) {
    els.productsGrid.innerHTML = '<article class="empty-card">Faça login para visualizar e manter o catálogo.</article>';
    els.productsCountLabel.textContent = '0 itens';
    els.productsPageLabel.textContent = 'Página 1';
    return;
  }

  if (!state.products.length) {
    els.productsGrid.innerHTML = '<article class="empty-card">Nenhum produto encontrado para os filtros aplicados.</article>';
  } else {
    els.productsGrid.innerHTML = state.products.map((product) => `
      <article class="product-card">
        <header>
          <div>
            <h4>${escapeHtml(product.name)}</h4>
            <small class="text-body-secondary">${escapeHtml(product.category?.name || 'Sem categoria')}</small>
          </div>
          <span class="badge ${product.available ? 'text-bg-success' : 'text-bg-secondary'}">${product.available ? 'Disponível' : 'Indisponível'}</span>
        </header>
        <p>${escapeHtml(product.description || 'Sem descrição')}</p>
        <div class="product-meta-grid">
          <div>
            <span>Preço</span>
            <strong>${formatCurrency(product.price)}</strong>
          </div>
          <div>
            <span>ID</span>
            <strong>#${product.id}</strong>
          </div>
        </div>
        <div class="card-actions">
          <button class="btn btn-sm btn-outline-dark" type="button" data-action="edit-product" data-id="${product.id}">Editar</button>
          <button class="btn btn-sm btn-outline-danger" type="button" data-action="delete-product" data-id="${product.id}">Excluir</button>
        </div>
      </article>
    `).join('');
  }

  const total = state.productsMeta?.total ?? state.products.length;
  const currentPage = state.productsMeta?.current_page ?? 1;
  const lastPage = state.productsMeta?.last_page ?? 1;

  els.productsCountLabel.textContent = `${total} item(ns)`;
  els.productsPageLabel.textContent = `Página ${currentPage} de ${lastPage}`;
  els.previousPageButton.disabled = currentPage <= 1;
  els.nextPageButton.disabled = currentPage >= lastPage;
}

async function saveProduct(event) {
  event.preventDefault();

  if (!state.token) {
    notify('Faça login para salvar produtos.');
    return;
  }

  const formData = new FormData(event.currentTarget);
  const id = formData.get('id');
  const payload = {
    category_id: Number(formData.get('category_id')),
    name: formData.get('name'),
    description: formData.get('description') || null,
    price: Number(formData.get('price')),
    available: formData.get('available') === '1',
  };

  try {
    if (id) {
      await request(`/api/products/${id}`, { method: 'PUT', body: payload });
      notify('Produto atualizado com sucesso.');
    } else {
      await request('/api/products', { method: 'POST', body: payload });
      notify('Produto criado com sucesso.');
    }

    resetProductForm();
    await loadProducts(state.currentPage);
    await loadCategories();
  } catch (error) {
    notify(error.message);
  }
}

function resetProductForm() {
  els.productForm.reset();
  document.getElementById('productId').value = '';
  els.productFormTitle.textContent = 'Novo produto';
}

function editProduct(id) {
  const product = state.products.find((item) => String(item.id) === String(id));

  if (!product) {
    return;
  }

  document.getElementById('productId').value = product.id;
  document.getElementById('productCategory').value = product.category_id;
  document.getElementById('productName').value = product.name;
  document.getElementById('productDescription').value = product.description || '';
  document.getElementById('productPrice').value = product.price;
  document.getElementById('productAvailable').value = product.available ? '1' : '0';
  els.productFormTitle.textContent = `Editar: ${product.name}`;
  window.scrollTo({ top: document.getElementById('productsSection').offsetTop - 20, behavior: 'smooth' });
}

async function deleteProduct(id) {
  if (!confirm('Excluir este produto?')) {
    return;
  }

  try {
    await request(`/api/products/${id}`, { method: 'DELETE' });
    notify('Produto excluído.');
    resetProductForm();
    await loadProducts(state.currentPage);
    await loadCategories();
  } catch (error) {
    notify(error.message);
  }
}

async function refreshProtectedData() {
  await loadCategories();
  await loadProducts(1);
}

function formatCurrency(value) {
  const number = Number(value || 0);
  return new Intl.NumberFormat('pt-BR', {
    style: 'currency',
    currency: 'BRL',
  }).format(number);
}

function escapeHtml(value) {
  return String(value)
    .replaceAll('&', '&amp;')
    .replaceAll('<', '&lt;')
    .replaceAll('>', '&gt;')
    .replaceAll('"', '&quot;')
    .replaceAll("'", '&#039;');
}

els.registerForm.addEventListener('submit', registerUser);
els.loginForm.addEventListener('submit', loginUser);
els.logoutButton.addEventListener('click', logoutUser);
els.categoryForm.addEventListener('submit', saveCategory);
els.resetCategoryFormButton.addEventListener('click', resetCategoryForm);
els.refreshCategoriesButton.addEventListener('click', loadCategories);
els.productForm.addEventListener('submit', saveProduct);
els.resetProductFormButton.addEventListener('click', resetProductForm);
els.productFiltersForm.addEventListener('submit', (event) => {
  event.preventDefault();
  loadProducts(1);
});
els.resetFiltersButton.addEventListener('click', () => {
  els.productFiltersForm.reset();
  loadProducts(1);
});
els.refreshProductsButton.addEventListener('click', () => loadProducts(state.currentPage));
els.previousPageButton.addEventListener('click', () => loadProducts((state.productsMeta?.current_page || 1) - 1));
els.nextPageButton.addEventListener('click', () => loadProducts((state.productsMeta?.current_page || 1) + 1));

els.categoriesTableBody.addEventListener('click', (event) => {
  const button = event.target.closest('[data-action]');

  if (!button) {
    return;
  }

  const { action, id } = button.dataset;

  if (action === 'edit-category') {
    editCategory(id);
  }

  if (action === 'delete-category') {
    deleteCategory(id);
  }
});

els.productsGrid.addEventListener('click', (event) => {
  const button = event.target.closest('[data-action]');

  if (!button) {
    return;
  }

  const { action, id } = button.dataset;

  if (action === 'edit-product') {
    editProduct(id);
  }

  if (action === 'delete-product') {
    deleteProduct(id);
  }
});

syncAuthView();
loadStatus();
refreshProtectedData();
