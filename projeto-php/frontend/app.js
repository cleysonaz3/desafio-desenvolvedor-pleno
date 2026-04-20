const state = {
  token: null,
  user: null,
  authPanel: 'login',
  categories: [],
  products: [],
  productsMeta: null,
  currentPage: 1,
};

const els = {
  authGate: document.getElementById('authGate'),
  dashboardRoot: document.getElementById('dashboardRoot'),
  gateLoginForm: document.getElementById('gateLoginForm'),
  gateLoginEmail: document.getElementById('gateLoginEmail'),
  gateLoginPassword: document.getElementById('gateLoginPassword'),
  fillDemoCredentialsButton: document.getElementById('fillDemoCredentialsButton'),
  showLoginPanelButton: document.getElementById('showLoginPanelButton'),
  showRegisterPanelButton: document.getElementById('showRegisterPanelButton'),
  switchToRegisterButton: document.getElementById('switchToRegisterButton'),
  switchToLoginButton: document.getElementById('switchToLoginButton'),
  loginPanel: document.getElementById('loginPanel'),
  registerPanel: document.getElementById('registerPanel'),
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
  productImageUrl: document.getElementById('productImageUrl'),
  productImagePreview: document.getElementById('productImagePreview'),
  productImagePreviewEmpty: document.getElementById('productImagePreviewEmpty'),
  productBulkImportForm: document.getElementById('productBulkImportForm'),
  productBulkPayload: document.getElementById('productBulkPayload'),
  fillImportTemplateButton: document.getElementById('fillImportTemplateButton'),
  clearImportPayloadButton: document.getElementById('clearImportPayloadButton'),
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

const PRODUCT_VISUALS = [
  {
    matcher: /vanilla whey|whey.*baunilha/i,
    image: 'https://d3eomlzmsu8e9b.cloudfront.net/media/catalog/product/cache/31c30eac1a1b5c34a29d797d955fb1c3/v/a/vanilla_whey_latao_1308x1636px_1.jpg',
    kicker: 'Proteínas',
    tone: 'Nova fórmula',
    caption: 'Proteína hidrolisada e isolada com colágeno em peptídeos.',
    tint: 'rgba(196, 154, 109, 0.34)',
  },
  {
    matcher: /a[cç]a[ií] whey|whey.*a[cç]a[ií]/i,
    image: 'https://d3eomlzmsu8e9b.cloudfront.net/media/catalog/product/cache/26856556595e8df4b5035801b01cd909/a/c/acai_whey_lata_media_1308x1636px.jpg',
    kicker: 'Best-seller',
    tone: 'Proteínas',
    caption: 'Blend proteico com açaí orgânico do Pará e banana.',
    tint: 'rgba(151, 102, 67, 0.3)',
  },
  {
    matcher: /h\.?i\.? whey|whey.*sem sabor|whey.*neutro/i,
    image: 'https://d3eomlzmsu8e9b.cloudfront.net/media/catalog/product/cache/26856556595e8df4b5035801b01cd909/h/i/hi_whey_1308x1636px_1.jpg',
    kicker: 'Proteínas',
    tone: 'Sem sabor',
    caption: 'Whey hidrolisado e isolado, sem aromas e adoçantes.',
    tint: 'rgba(181, 154, 132, 0.28)',
  },
  {
    matcher: /crealift|creatina/i,
    image: 'https://d3eomlzmsu8e9b.cloudfront.net/media/catalog/product/cache/31c30eac1a1b5c34a29d797d955fb1c3/c/r/crealift_lata_pequena_01.jpg',
    kicker: 'Treino',
    tone: 'Vegan',
    caption: 'Creatina mono-hidratada com Creapure e linguagem premium.',
    tint: 'rgba(151, 112, 78, 0.3)',
  },
  {
    matcher: /super omega|ômega|omega/i,
    image: 'https://d3eomlzmsu8e9b.cloudfront.net/media/wysiwyg/produtos/omega/mobile/omega-3-essential-vistas.jpg',
    kicker: 'Ômega-3',
    tone: 'Pureza',
    caption: 'Linha ômega com foco em pureza, EPA e DHA.',
    tint: 'rgba(170, 126, 78, 0.3)',
  },
  {
    matcher: /vitalift|mg complex|magn[eé]sio/i,
    image: 'https://d3eomlzmsu8e9b.cloudfront.net/media/wysiwyg/home/linha-clinica.jpg',
    kicker: 'Vitaminas',
    tone: 'Bem-estar',
    caption: 'Suplementos para rotina diária, equilíbrio e suporte metabólico.',
    tint: 'rgba(154, 123, 90, 0.28)',
  },
];

const CATEGORY_VISUALS = [
  {
    matcher: /prote[ií]nas?/i,
    kicker: 'Proteínas',
    tone: 'Catálogo',
    caption: 'Proteínas premium para massa magra e recuperação.',
    tint: 'rgba(188, 154, 126, 0.26)',
  },
  {
    matcher: /treino|performance/i,
    kicker: 'Treino',
    tone: 'Alta performance',
    caption: 'Produtos voltados para força, energia e performance física.',
    tint: 'rgba(150, 112, 76, 0.26)',
  },
  {
    matcher: /bem-estar|vitaminas|minerais/i,
    kicker: 'Bem-estar',
    tone: 'Rotina',
    caption: 'Soluções para equilíbrio nutricional e cuidado diário.',
    tint: 'rgba(176, 145, 115, 0.24)',
  },
  {
    matcher: /omega|ômega/i,
    kicker: 'Ômega-3',
    tone: 'Pureza',
    caption: 'Linha essencial para cuidado cardiovascular e cognitivo.',
    tint: 'rgba(178, 134, 83, 0.26)',
  },
];

const BULK_IMPORT_TEMPLATE = [
  {
    category_name: 'Proteínas',
    name: 'Whey Blend 3W',
    description: 'Blend proteico para rotina diária.',
    image_url: 'https://cdn.seusite.com/produtos/whey-blend-3w.png',
    price: 169.90,
    available: true,
  },
  {
    category_name: 'Bem-estar',
    name: 'Multivitamínico Daily',
    description: 'Suporte nutricional para o dia a dia.',
    image_url: 'https://cdn.seusite.com/produtos/multi-daily.png',
    price: 98.50,
    available: true,
  },
];

function getApiPath(path) {
  return path;
}

async function request(path, options = {}) {
  const headers = new Headers(options.headers || {});
  headers.set('Accept', 'application/json');

  if (options.body && !headers.has('Content-Type')) {
    headers.set('Content-Type', 'application/json');
  }

  if (options.frontendCookieAuth) {
    headers.set('X-Frontend-Auth', 'cookie');
  }

  const response = await fetch(getApiPath(path), {
    ...options,
    credentials: 'same-origin',
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

function syncAuthenticatedLayout() {
  const connected = Boolean(state.user);
  els.authGate.hidden = connected;
  els.dashboardRoot.hidden = !connected;
}

function syncAuthPanels() {
  const showLogin = state.authPanel === 'login';

  els.loginPanel.hidden = !showLogin;
  els.registerPanel.hidden = showLogin;
  els.showLoginPanelButton.classList.toggle('is-active', showLogin);
  els.showRegisterPanelButton.classList.toggle('is-active', !showLogin);
}

function setAuthPanel(panel) {
  state.authPanel = panel === 'register' ? 'register' : 'login';
  syncAuthPanels();
}

function setAuthState(token, user) {
  state.token = token;
  state.user = user;

  syncAuthenticatedLayout();
  syncAuthView();
}

function syncAuthView() {
  const connected = Boolean(state.user);
  els.authStateBadge.textContent = connected ? 'autenticado' : 'desconectado';
  els.authStateBadge.className = `badge ${connected ? 'text-bg-success' : 'text-bg-dark'}`;
  els.currentUserName.textContent = state.user?.name || '-';
  els.currentUserEmail.textContent = state.user?.email || '-';
}

function syncProductImagePreview(url = '') {
  const normalizedUrl = String(url || '').trim();

  if (!normalizedUrl) {
    els.productImagePreview.hidden = true;
    els.productImagePreview.removeAttribute('src');
    els.productImagePreviewEmpty.hidden = false;
    return;
  }

  els.productImagePreview.src = normalizedUrl;
  els.productImagePreview.hidden = false;
  els.productImagePreviewEmpty.hidden = true;
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
      frontendCookieAuth: true,
      body: Object.fromEntries(formData.entries()),
    });

    setAuthState(null, data.data.user);
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
      frontendCookieAuth: true,
      body: Object.fromEntries(formData.entries()),
    });

    setAuthState(null, data.data.user);
    notify('Login realizado com sucesso.');
    await refreshProtectedData();
  } catch (error) {
    notify(error.message);
  }
}

async function loginFromGate(event) {
  event.preventDefault();
  const formData = new FormData(event.currentTarget);

  try {
    const data = await request('/api/login', {
      method: 'POST',
      frontendCookieAuth: true,
      body: Object.fromEntries(formData.entries()),
    });

    setAuthState(null, data.data.user);
    notify('Login realizado com sucesso.');
    await refreshProtectedData();
  } catch (error) {
    notify(error.message);
  }
}

async function logoutUser() {
  if (!state.user) {
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

async function loadCurrentUser() {
  try {
    const data = await request('/api/me');
    setAuthState(null, data.data);
    return true;
  } catch {
    setAuthState(null, null);
    return false;
  }
}

async function loadCategories() {
  if (!state.user) {
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
  if (!state.user) {
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

  if (!state.user) {
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

  if (!state.user) {
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
  if (!state.user) {
    els.productsGrid.innerHTML = '<article class="empty-card">Faça login para visualizar e manter o catálogo.</article>';
    els.productsCountLabel.textContent = '0 itens';
    els.productsPageLabel.textContent = 'Página 1';
    return;
  }

  if (!state.products.length) {
    els.productsGrid.innerHTML = '<article class="empty-card">Nenhum produto encontrado para os filtros aplicados.</article>';
  } else {
    els.productsGrid.innerHTML = state.products.map((product) => renderProductCard(product)).join('');
  }

  const total = state.productsMeta?.total ?? state.products.length;
  const currentPage = state.productsMeta?.current_page ?? 1;
  const lastPage = state.productsMeta?.last_page ?? 1;

  els.productsCountLabel.textContent = `${total} item(ns)`;
  els.productsPageLabel.textContent = `Página ${currentPage} de ${lastPage}`;
  els.previousPageButton.disabled = currentPage <= 1;
  els.nextPageButton.disabled = currentPage >= lastPage;
}

function renderProductCard(product) {
  const visual = getProductVisual(product);
  const imageMarkup = visual.image
    ? `<div class="product-media-frame"><img src="${escapeHtml(visual.image)}" alt="${escapeHtml(product.name)}" loading="lazy" onerror="this.closest('.product-media-frame').classList.add('media-fallback'); this.remove();"></div>`
    : '<div class="product-media-frame media-fallback"><div class="placeholder-art" aria-hidden="true"></div></div>';

  return `
    <article class="product-card" style="--product-tint:${visual.tint};">
      <div class="product-media">
        <div class="product-badge-strip">
          <span class="product-kicker">${escapeHtml(visual.kicker)}</span>
          <span class="product-tone">${escapeHtml(visual.tone)}</span>
        </div>
        ${imageMarkup}
      </div>
      <div class="product-body">
        <header>
          <div>
            <h4>${escapeHtml(product.name)}</h4>
            <small class="text-body-secondary">${escapeHtml(product.category?.name || 'Sem categoria')}</small>
          </div>
          <span class="badge ${product.available ? 'text-bg-success' : 'text-bg-secondary'}">${product.available ? 'Disponível' : 'Indisponível'}</span>
        </header>
        <div class="product-caption">${escapeHtml(visual.caption)}</div>
        <p>${escapeHtml(product.description || 'Sem descrição')}</p>
        <div class="product-meta-grid">
          <div class="meta-box">
            <span>Preço</span>
            <strong>${formatCurrency(product.price)}</strong>
          </div>
          <div class="meta-box">
            <span>ID</span>
            <strong>#${product.id}</strong>
          </div>
          <div class="meta-box">
            <span>Status</span>
            <strong>${product.available ? 'Em estoque' : 'Indisponível'}</strong>
          </div>
        </div>
        <div class="card-actions">
          <button class="btn btn-sm btn-outline-dark" type="button" data-action="edit-product" data-id="${product.id}">Editar</button>
          <button class="btn btn-sm btn-outline-danger" type="button" data-action="delete-product" data-id="${product.id}">Excluir</button>
        </div>
      </div>
    </article>
  `;
}

async function saveProduct(event) {
  event.preventDefault();

  if (!state.user) {
    notify('Faça login para salvar produtos.');
    return;
  }

  const formData = new FormData(event.currentTarget);
  const id = formData.get('id');
  const payload = {
    category_id: Number(formData.get('category_id')),
    name: formData.get('name'),
    description: formData.get('description') || null,
    image_url: formData.get('image_url') || null,
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
  syncProductImagePreview('');
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
  document.getElementById('productImageUrl').value = product.image_url || '';
  document.getElementById('productPrice').value = product.price;
  document.getElementById('productAvailable').value = product.available ? '1' : '0';
  els.productFormTitle.textContent = `Editar: ${product.name}`;
  syncProductImagePreview(product.image_url || '');
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

function fillImportTemplate() {
  if (!els.productBulkPayload) {
    return;
  }

  els.productBulkPayload.value = JSON.stringify(BULK_IMPORT_TEMPLATE, null, 2);
}

function clearImportPayload() {
  if (!els.productBulkPayload) {
    return;
  }

  els.productBulkPayload.value = '';
}

async function importProductsBatch(event) {
  event.preventDefault();

  if (!state.user) {
    notify('Faça login para importar produtos.');
    return;
  }

  const rawPayload = String(els.productBulkPayload?.value || '').trim();

  if (!rawPayload) {
    notify('Informe um JSON válido para importar.');
    return;
  }

  let parsedPayload;

  try {
    parsedPayload = JSON.parse(rawPayload);
  } catch {
    notify('JSON inválido. Revise o formato antes de importar.');
    return;
  }

  if (!Array.isArray(parsedPayload) || parsedPayload.length === 0) {
    notify('O payload deve ser um array de produtos.');
    return;
  }

  try {
    const data = await request('/api/products/import', {
      method: 'POST',
      body: {
        items: parsedPayload,
      },
    });

    notify(`Importação concluída: ${data.imported_count ?? 0} produto(s) criado(s).`);
    clearImportPayload();
    await loadCategories();
    await loadProducts(1);
  } catch (error) {
    notify(error.message);
  }
}

async function refreshProtectedData() {
  if (!state.user) {
    renderCategories();
    renderProducts();
    return;
  }

  await loadCategories();
  await loadProducts(1);
}

function getProductVisual(product) {
  if (product.image_url) {
    return {
      image: product.image_url,
      kicker: product.category?.name || 'Catálogo',
      tone: 'Imagem própria',
      caption: 'Imagem personalizada cadastrada diretamente no produto.',
      tint: 'rgba(143, 95, 61, 0.22)',
    };
  }

  const haystack = `${product.name} ${product.description || ''}`.trim();
  const matchedVisual = PRODUCT_VISUALS.find((visual) => visual.matcher.test(haystack));

  if (matchedVisual) {
    return matchedVisual;
  }

  const categoryVisual = CATEGORY_VISUALS.find((visual) => visual.matcher.test(product.category?.name || ''));

  return {
    image: '',
    kicker: categoryVisual?.kicker || 'Catálogo',
    tone: categoryVisual?.tone || 'Essential',
    caption: categoryVisual?.caption || 'Produto sincronizado pela API do catálogo.',
    tint: categoryVisual?.tint || 'rgba(143, 95, 61, 0.2)',
  };
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

function fillDemoCredentials() {
  els.gateLoginEmail.value = 'demo@example.com';
  els.gateLoginPassword.value = 'password';
  setAuthPanel('login');
}

els.registerForm?.addEventListener('submit', registerUser);
els.gateLoginForm.addEventListener('submit', loginFromGate);
els.loginForm?.addEventListener('submit', loginUser);
els.logoutButton?.addEventListener('click', logoutUser);
els.fillDemoCredentialsButton.addEventListener('click', fillDemoCredentials);
els.showLoginPanelButton.addEventListener('click', () => setAuthPanel('login'));
els.showRegisterPanelButton.addEventListener('click', () => setAuthPanel('register'));
els.switchToRegisterButton.addEventListener('click', () => setAuthPanel('register'));
els.switchToLoginButton.addEventListener('click', () => setAuthPanel('login'));
els.categoryForm.addEventListener('submit', saveCategory);
els.resetCategoryFormButton.addEventListener('click', resetCategoryForm);
els.refreshCategoriesButton.addEventListener('click', loadCategories);
els.productForm.addEventListener('submit', saveProduct);
els.resetProductFormButton.addEventListener('click', resetProductForm);
els.productBulkImportForm?.addEventListener('submit', importProductsBatch);
els.fillImportTemplateButton?.addEventListener('click', fillImportTemplate);
els.clearImportPayloadButton?.addEventListener('click', clearImportPayload);
els.productImageUrl.addEventListener('input', (event) => {
  syncProductImagePreview(event.currentTarget.value);
});
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
syncAuthPanels();
syncProductImagePreview(els.productImageUrl?.value || '');
loadStatus();
loadCurrentUser().then((authenticated) => {
  if (authenticated) {
    refreshProtectedData();
  } else {
    renderCategories();
    renderProducts();
  }
});
