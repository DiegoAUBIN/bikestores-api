const storageKey = 'bikestores.session';
const rootPath = window.location.pathname.split('/site')[0] || '';
const apiBase = `${window.location.origin}${rootPath}`;
const writeApiKey = 'e8f1997c763';

const state = {
    session: null,
    brands: [],
    categories: [],
    products: [],
    employees: [],
};

const bikeFallbackImage = `data:image/svg+xml,${encodeURIComponent('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 520"><rect width="800" height="520" fill="#0f172a"/><circle cx="250" cy="360" r="82" fill="none" stroke="#f97316" stroke-width="16"/><circle cx="560" cy="360" r="82" fill="none" stroke="#f97316" stroke-width="16"/><path d="M250 360 L345 250 L420 360 L500 360 L420 230" fill="none" stroke="#f8fafc" stroke-width="14" stroke-linecap="round" stroke-linejoin="round"/><path d="M420 230 L390 230" fill="none" stroke="#f8fafc" stroke-width="12" stroke-linecap="round"/></svg>')}`;

const brandImageSets = {
    electra: '/site/assets/images/brands/electra.jpg',
    haro: '/site/assets/images/brands/haro.jpg',
    heller: '/site/assets/images/brands/heller.jpg',
    'pure cycles': '/site/assets/images/brands/pure-cycles.jpg',
    ritchey: '/site/assets/images/brands/ritchey.jpg',
    strider: '/site/assets/images/brands/strider.jpg',
    'sun bicycles': '/site/assets/images/brands/sun-bicycles.jpg',
    surly: '/site/assets/images/brands/surly.jpg',
    trek: '/site/assets/images/brands/trek.jpg',
};

const categoryImageSets = {
    mountain: '/site/assets/images/categories/mountain.jpg',
    road: '/site/assets/images/categories/road.jpg',
    electric: '/site/assets/images/categories/electric.jpg',
    kids: '/site/assets/images/categories/kids.jpg',
    city: '/site/assets/images/categories/city.jpg',
    default: '/site/assets/images/categories/mountain.jpg',
};

const productSpecificImages = {
    'trek 820 - 2016': '/site/assets/images/products/trek-820-2016.jpg',
    'trek fuel ex 8 29 - 2016': '/site/assets/images/products/trek-fuel-ex-8-2016.jpg',
};

const loginSection = document.getElementById('loginSection');
const appSection = document.getElementById('appSection');
const loginForm = document.getElementById('loginForm');
const loginButton = document.getElementById('loginButton');
const loginAlert = document.getElementById('loginAlert');
const appAlert = document.getElementById('appAlert');
const userBadge = document.getElementById('userBadge');
const logoutButton = document.getElementById('logoutButton');
const productsGrid = document.getElementById('productsGrid');
const featuredRail = document.getElementById('featuredRail');
const productsCount = document.getElementById('productsCount');
const averagePrice = document.getElementById('averagePrice');
const employeesTableBody = document.getElementById('employeesTableBody');
const employeesCount = document.getElementById('employeesCount');
const employeesHint = document.getElementById('employeesHint');
const brandFilter = document.getElementById('brandFilter');
const categoryFilter = document.getElementById('categoryFilter');
const yearFilter = document.getElementById('yearFilter');
const minPriceFilter = document.getElementById('minPriceFilter');
const maxPriceFilter = document.getElementById('maxPriceFilter');
const applyFiltersButton = document.getElementById('applyFiltersButton');
const resetFiltersButton = document.getElementById('resetFiltersButton');
const employeesTab = document.getElementById('employees-tab');
const operationsTab = document.getElementById('operations-tab');
const accountTab = document.getElementById('account-tab');
const crudForm = document.getElementById('crudForm');
const crudResource = document.getElementById('crudResource');
const crudAction = document.getElementById('crudAction');
const crudId = document.getElementById('crudId');
const crudIdLabel = document.getElementById('crudIdLabel');
const crudHint = document.getElementById('crudHint');
const crudBrandName = document.getElementById('crudBrandName');
const crudCategoryName = document.getElementById('crudCategoryName');
const crudStoreName = document.getElementById('crudStoreName');
const crudStorePhone = document.getElementById('crudStorePhone');
const crudStoreEmail = document.getElementById('crudStoreEmail');
const crudStoreStreet = document.getElementById('crudStoreStreet');
const crudStoreCity = document.getElementById('crudStoreCity');
const crudStoreState = document.getElementById('crudStoreState');
const crudStoreZip = document.getElementById('crudStoreZip');
const crudProductName = document.getElementById('crudProductName');
const crudProductBrandId = document.getElementById('crudProductBrandId');
const crudProductCategoryId = document.getElementById('crudProductCategoryId');
const crudProductModelYear = document.getElementById('crudProductModelYear');
const crudProductPrice = document.getElementById('crudProductPrice');
const crudStockStoreId = document.getElementById('crudStockStoreId');
const crudStockProductId = document.getElementById('crudStockProductId');
const crudStockQuantity = document.getElementById('crudStockQuantity');
const crudGroups = document.querySelectorAll('[data-crud-group]');
const employeeCreateForm = document.getElementById('employeeCreateForm');
const newEmployeeName = document.getElementById('newEmployeeName');
const newEmployeeEmail = document.getElementById('newEmployeeEmail');
const newEmployeePassword = document.getElementById('newEmployeePassword');
const newEmployeeRole = document.getElementById('newEmployeeRole');
const newEmployeeStore = document.getElementById('newEmployeeStore');
const profileForm = document.getElementById('profileForm');
const profileName = document.getElementById('profileName');
const profileEmail = document.getElementById('profileEmail');
const profilePassword = document.getElementById('profilePassword');
const quickEmployeesAction = document.querySelector('[data-target-tab="employees-tab"]');

/**
 * Sends an HTTP request to the API and returns decoded JSON.
 *
 * @param {string} path
 * @param {RequestInit} options
 * @returns {Promise<any>}
 */
async function apiRequest(path, options = {}) {
    const response = await fetch(`${apiBase}${path}`, {
        ...options,
        credentials: 'same-origin',
        headers: {
            'Content-Type': 'application/json',
            ...(options.headers || {}),
        },
    });

    const body = await response.json().catch(() => ({}));

    if (!response.ok) {
        throw new Error(body.error || body.message || 'Request failed.');
    }

    return body;
}

/**
 * Adds write-security headers used by protected endpoints.
 *
 * @param {RequestInit} options
 * @returns {RequestInit}
 */
function withWriteAuth(options = {}) {
    return {
        ...options,
        headers: {
            ...(options.headers || {}),
            'X-API-KEY': writeApiKey,
        },
    };
}

/**
 * Displays an alert message.
 *
 * @param {HTMLElement} element
 * @param {string} message
 * @param {'success'|'danger'|'warning'|'info'} type
 */
function showAlert(element, message, type) {
    element.className = `alert alert-${type}`;
    element.textContent = message;
    element.classList.remove('d-none');
}

/**
 * Hides an alert message.
 *
 * @param {HTMLElement} element
 */
function hideAlert(element) {
    element.classList.add('d-none');
    element.textContent = '';
}

/**
 * Saves the authenticated session to web storage.
 *
 * @param {any} employee
 */
function saveSession(employee) {
    state.session = employee;
    sessionStorage.setItem(storageKey, JSON.stringify(employee));
}

/**
 * Loads a previously stored session.
 */
function restoreSession() {
    const rawSession = sessionStorage.getItem(storageKey);

    if (!rawSession) {
        return;
    }

    try {
        state.session = JSON.parse(rawSession);
    } catch (error) {
        sessionStorage.removeItem(storageKey);
    }
}

/**
 * Ends the current session and returns to the login form.
 */
async function logout() {
    try {
        await apiRequest('/auth/logout', { method: 'POST' });
    } catch (error) {
        // Keep client logout flow even if server session is already gone.
    }

    state.session = null;
    sessionStorage.removeItem(storageKey);
    loginSection.classList.remove('d-none');
    appSection.classList.add('d-none');
    loginForm.reset();
    hideAlert(appAlert);
}

/**
 * Populates brand, category, and year filters.
 */
function renderFilters() {
    const years = [...new Set(state.products.map((item) => item.model_year))].sort((a, b) => b - a);

    brandFilter.innerHTML = '<option value="">All brands</option>';
    categoryFilter.innerHTML = '<option value="">All categories</option>';
    yearFilter.innerHTML = '<option value="">All years</option>';

    state.brands.forEach((brand) => {
        brandFilter.insertAdjacentHTML('beforeend', `<option value="${brand.brand_id}">${brand.brand_name}</option>`);
    });

    state.categories.forEach((category) => {
        categoryFilter.insertAdjacentHTML('beforeend', `<option value="${category.category_id}">${category.category_name}</option>`);
    });

    years.forEach((year) => {
        yearFilter.insertAdjacentHTML('beforeend', `<option value="${year}">${year}</option>`);
    });
}

/**
 * Returns products matching the active filters.
 *
 * @returns {any[]}
 */
function getFilteredProducts() {
    const selectedBrand = brandFilter.value;
    const selectedCategory = categoryFilter.value;
    const selectedYear = yearFilter.value;
    const minPrice = minPriceFilter.value === '' ? null : Number(minPriceFilter.value);
    const maxPrice = maxPriceFilter.value === '' ? null : Number(maxPriceFilter.value);

    return state.products.filter((product) => {
        if (selectedBrand !== '' && String(product.brand_id) !== selectedBrand) {
            return false;
        }

        if (selectedCategory !== '' && String(product.category_id) !== selectedCategory) {
            return false;
        }

        if (selectedYear !== '' && String(product.model_year) !== selectedYear) {
            return false;
        }

        const price = Number(product.list_price);

        if (minPrice !== null && price < minPrice) {
            return false;
        }

        if (maxPrice !== null && price > maxPrice) {
            return false;
        }

        return true;
    });
}

/**
 * Returns a deterministic image URL for one product card.
 *
 * @param {any} product
 * @param {number} index
 * @returns {string}
 */
function getProductImageUrl(product, index) {
    const normalizedProductName = String(product.product_name || '').trim().toLowerCase();

    if (productSpecificImages[normalizedProductName]) {
        return productSpecificImages[normalizedProductName];
    }

    const categoryId = Number(product.category_id);
    const categoryGroup = categoryId === 6
        ? 'mountain'
        : categoryId === 7 || categoryId === 4
            ? 'road'
            : categoryId === 5
                ? 'electric'
                : categoryId === 1
                    ? 'kids'
                    : 'city';

    const brandName = String(
        state.brands.find((brand) => Number(brand.brand_id) === Number(product.brand_id))?.brand_name || ''
    ).trim().toLowerCase();

    const byBrand = brandImageSets[brandName];

    if (byBrand) {
        return byBrand;
    }

    const byCategory = categoryImageSets[categoryGroup] || categoryImageSets.default;

    return byCategory;
}

/**
 * Builds product card HTML.
 *
 * @param {any} item
 * @param {Map<number, string>} brandMap
 * @param {Map<number, string>} categoryMap
 * @param {number} index
 * @returns {string}
 */
function buildProductCard(item, brandMap, categoryMap, index) {
    const brandName = brandMap.get(item.brand_id) || `Brand #${item.brand_id}`;
    const categoryName = categoryMap.get(item.category_id) || `Category #${item.category_id}`;

    return `
        <article class="product-card fade-up">
            <img src="${getProductImageUrl(item, index)}" alt="${item.product_name}" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='${bikeFallbackImage}';">
            <div class="body">
                <p class="card-title-tight">${item.product_name}</p>
                <div class="card-meta">
                    <span>${brandName}</span>
                    <span>${item.model_year}</span>
                </div>
                <div class="card-meta">
                    <span>${categoryName}</span>
                    <span>ID #${item.product_id}</span>
                </div>
                <span class="price-tag">$${Number(item.list_price).toFixed(2)}</span>
            </div>
        </article>
    `;
}

/**
 * Renders catalog cards and featured cards.
 *
 * @param {any[]} products
 */
function renderProducts(products) {
    const brandMap = new Map(state.brands.map((item) => [item.brand_id, item.brand_name]));
    const categoryMap = new Map(state.categories.map((item) => [item.category_id, item.category_name]));

    productsCount.textContent = String(products.length);

    const average = products.length > 0
        ? products.reduce((sum, item) => sum + Number(item.list_price), 0) / products.length
        : 0;

    averagePrice.textContent = `$${average.toFixed(0)}`;

    productsGrid.innerHTML = products
        .map((item, index) => buildProductCard(item, brandMap, categoryMap, index))
        .join('');

    if (products.length === 0) {
        productsGrid.innerHTML = '<div class="alert alert-secondary">No products found for these filters.</div>';
    }

    const featured = products.slice(0, 4);
    featuredRail.innerHTML = featured
        .map((item, index) => {
            const brandName = brandMap.get(item.brand_id) || `Brand #${item.brand_id}`;

            return `
                <article class="featured-card fade-up">
                    <img src="${getProductImageUrl(item, index + 15)}" alt="${item.product_name}" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='${bikeFallbackImage}';">
                    <div class="body">
                        <p class="card-title-tight">${item.product_name}</p>
                        <div class="card-meta">
                            <span>${brandName}</span>
                            <span>${item.model_year}</span>
                        </div>
                        <span class="price-tag">$${Number(item.list_price).toFixed(2)}</span>
                    </div>
                </article>
            `;
        })
        .join('');
}

/**
 * Renders the employee list based on the current role.
 */
function renderEmployees() {
    if (!state.session) {
        return;
    }

    if (state.session.employee_role === 'employee') {
        employeesHint.textContent = 'Your role cannot view employee records.';
        employeesTableBody.innerHTML = '';
        employeesCount.textContent = '0 members';
        return;
    }

    let visibleEmployees = state.employees;

    if (state.session.employee_role === 'chief') {
        visibleEmployees = state.employees.filter((employee) => employee.store_id === state.session.store_id);
        employeesHint.textContent = 'Showing employees in your store.';
    } else {
        employeesHint.textContent = 'Showing employees in all stores.';
    }

    employeesTableBody.innerHTML = visibleEmployees.map((employee) => {
        return `
            <tr>
                <td>${employee.employee_id}</td>
                <td>${employee.employee_name}</td>
                <td>${employee.employee_email}</td>
                <td>${employee.employee_role}</td>
                <td>${employee.store_id}</td>
            </tr>
        `;
    }).join('');

    if (visibleEmployees.length === 0) {
        employeesTableBody.innerHTML = '<tr><td colspan="5" class="text-center text-secondary">No employees found.</td></tr>';
    }

    employeesCount.textContent = `${visibleEmployees.length} members`;
}

/**
 * Returns trimmed string value or null.
 *
 * @param {HTMLInputElement} input
 * @returns {string|null}
 */
function inputText(input) {
    const value = input.value.trim();
    return value === '' ? null : value;
}

/**
 * Returns numeric value or null.
 *
 * @param {HTMLInputElement} input
 * @returns {number|null}
 */
function inputNumber(input) {
    const raw = input.value.trim();
    if (raw === '') {
        return null;
    }

    const parsed = Number(raw);
    return Number.isFinite(parsed) ? parsed : null;
}

/**
 * Makes CRUD fields visible for selected resource and action.
 */
function syncCrudFieldsVisibility() {
    const resource = crudResource.value;
    const action = crudAction.value;
    const isDelete = action === 'delete';

    crudGroups.forEach((group) => {
        const groupResource = group.getAttribute('data-crud-group');
        const shouldHide = groupResource !== resource || (isDelete && resource !== 'stocks');
        group.classList.toggle('d-none', shouldHide);
    });

    const isStock = resource === 'stocks';
    crudId.closest('.col-12').classList.toggle('d-none', isStock);
    crudId.required = !isStock && action !== 'create';

    crudStockStoreId.required = isStock;
    crudStockProductId.required = isStock;
    crudStockQuantity.required = isStock && action !== 'delete';

    if (isStock && action === 'delete') {
        crudStockQuantity.closest('.col-12').classList.add('d-none');
    } else {
        crudStockQuantity.closest('.col-12').classList.remove('d-none');
    }

    const labels = {
        brands: 'Brand ID',
        categories: 'Category ID',
        stores: 'Store ID',
        products: 'Product ID',
    };

    if (!isStock) {
        crudIdLabel.textContent = labels[resource] || 'Identifier';
    }

    if (isStock) {
        crudHint.textContent = action === 'delete'
            ? 'For stock delete, enter Store ID and Product ID. Quantity is not required.'
            : 'For stocks, Store ID and Product ID are required. Quantity is required for create/update.';
    } else if (action === 'create') {
        crudHint.textContent = 'Create mode: fill the form fields for the selected resource.';
    } else if (action === 'update') {
        crudHint.textContent = 'Update mode: enter the resource ID and only the fields you want to change.';
    } else {
        crudHint.textContent = 'Delete mode: enter the resource ID, then submit.';
    }
}

/**
 * Builds payload from visible form fields.
 *
 * @param {string} resource
 * @param {string} action
 * @returns {any}
 */
function buildCrudPayload(resource, action) {
    if (action === 'delete') {
        return null;
    }

    if (resource === 'brands') {
        const payload = {};
        const brandName = inputText(crudBrandName);
        if (brandName !== null) {
            payload.brand_name = brandName;
        }
        return payload;
    }

    if (resource === 'categories') {
        const payload = {};
        const categoryName = inputText(crudCategoryName);
        if (categoryName !== null) {
            payload.category_name = categoryName;
        }
        return payload;
    }

    if (resource === 'stores') {
        const payload = {};
        const storeName = inputText(crudStoreName);
        const phone = inputText(crudStorePhone);
        const email = inputText(crudStoreEmail);
        const street = inputText(crudStoreStreet);
        const city = inputText(crudStoreCity);
        const stateCode = inputText(crudStoreState);
        const zipCode = inputText(crudStoreZip);

        if (storeName !== null) {
            payload.store_name = storeName;
        }

        if (phone !== null) {
            payload.phone = phone;
        }

        if (email !== null) {
            payload.email = email;
        }

        if (street !== null) {
            payload.street = street;
        }

        if (city !== null) {
            payload.city = city;
        }

        if (stateCode !== null) {
            payload.state = stateCode;
        }

        if (zipCode !== null) {
            payload.zip_code = zipCode;
        }

        return payload;
    }

    if (resource === 'products') {
        const payload = {};
        const productName = inputText(crudProductName);
        const brandId = inputNumber(crudProductBrandId);
        const categoryId = inputNumber(crudProductCategoryId);
        const modelYear = inputNumber(crudProductModelYear);
        const listPrice = inputNumber(crudProductPrice);

        if (productName !== null) {
            payload.product_name = productName;
        }

        if (brandId !== null) {
            payload.brand_id = brandId;
        }

        if (categoryId !== null) {
            payload.category_id = categoryId;
        }

        if (modelYear !== null) {
            payload.model_year = modelYear;
        }

        if (listPrice !== null) {
            payload.list_price = listPrice;
        }

        return payload;
    }

    if (resource === 'stocks') {
        const payload = {};
        const storeId = inputNumber(crudStockStoreId);
        const productId = inputNumber(crudStockProductId);
        const quantity = inputNumber(crudStockQuantity);

        if (storeId !== null) {
            payload.store_id = storeId;
        }

        if (productId !== null) {
            payload.product_id = productId;
        }

        if (quantity !== null) {
            payload.quantity = quantity;
        }

        return payload;
    }

    return {};
}

/**
 * Validates required fields for the selected operation.
 *
 * @param {string} resource
 * @param {string} action
 * @param {any} payload
 */
function validateCrudOperation(resource, action, payload) {
    if (resource !== 'stocks' && action !== 'create' && crudId.value.trim() === '') {
        throw new Error('Identifier is required for update/delete.');
    }

    if (resource === 'brands' && action !== 'delete' && !payload.brand_name) {
        throw new Error('Brand name is required.');
    }

    if (resource === 'categories' && action !== 'delete' && !payload.category_name) {
        throw new Error('Category name is required.');
    }

    if (resource === 'stores' && action === 'create' && !payload.store_name) {
        throw new Error('Store name is required.');
    }

    if (resource === 'products' && action === 'create') {
        const requiredKeys = ['product_name', 'brand_id', 'category_id', 'model_year', 'list_price'];
        const missing = requiredKeys.some((key) => !(key in payload));
        if (missing) {
            throw new Error('Product name, brand ID, category ID, model year and list price are required.');
        }
    }

    if (resource === 'stocks') {
        if (!payload.store_id || !payload.product_id) {
            throw new Error('Store ID and Product ID are required for stocks.');
        }

        if (action !== 'delete' && !Number.isInteger(payload.quantity)) {
            throw new Error('Quantity is required for stock create/update.');
        }
    }
}

/**
 * Executes CRUD request based on selected resource/action.
 *
 * @param {SubmitEvent} event
 */
async function handleCrudSubmit(event) {
    event.preventDefault();
    hideAlert(appAlert);

    const resource = crudResource.value;
    const action = crudAction.value;

    try {
        const payload = buildCrudPayload(resource, action);
        validateCrudOperation(resource, action, payload || {});

        let method = action === 'create' ? 'POST' : action === 'update' ? 'PUT' : 'DELETE';
        let path = `/${resource}`;

        if (resource === 'stocks') {
            if (action === 'update' || action === 'delete') {
                path = `/stocks/${payload.store_id}/${payload.product_id}`;
            }
        } else if (action !== 'create') {
            path = `/${resource}/${crudId.value.trim()}`;
        }

        const requestOptions = withWriteAuth({ method });

        if (payload !== null && action !== 'delete') {
            requestOptions.body = JSON.stringify(payload);
        }

        await apiRequest(path, requestOptions);
        showAlert(appAlert, `Operation ${action} on ${resource} completed.`, 'success');
        crudForm.reset();
        crudStockStoreId.value = String(state.session?.store_id || '');
        syncCrudFieldsVisibility();
        await loadApplicationData();
    } catch (error) {
        showAlert(appAlert, error.message || 'Operation failed.', 'danger');
    }
}

/**
 * Creates one employee (chief/it permissions only).
 *
 * @param {SubmitEvent} event
 */
async function handleEmployeeCreate(event) {
    event.preventDefault();
    hideAlert(appAlert);

    try {
        const payload = {
            employee_name: newEmployeeName.value.trim(),
            employee_email: newEmployeeEmail.value.trim(),
            employee_password: newEmployeePassword.value,
            employee_role: newEmployeeRole.value,
            store_id: Number(newEmployeeStore.value),
        };

        await apiRequest('/employees', withWriteAuth({
            method: 'POST',
            body: JSON.stringify(payload),
        }));

        employeeCreateForm.reset();
        newEmployeeStore.value = String(state.session?.store_id || '');
        showAlert(appAlert, 'Employee created successfully.', 'success');
        await loadApplicationData();
    } catch (error) {
        showAlert(appAlert, error.message || 'Employee creation failed.', 'danger');
    }
}

/**
 * Updates currently authenticated profile.
 *
 * @param {SubmitEvent} event
 */
async function handleProfileUpdate(event) {
    event.preventDefault();
    hideAlert(appAlert);

    try {
        const payload = {
            employee_name: profileName.value.trim(),
            employee_email: profileEmail.value.trim(),
        };

        if (profilePassword.value.trim() !== '') {
            payload.employee_password = profilePassword.value;
        }

        const response = await apiRequest('/auth/profile', withWriteAuth({
            method: 'PUT',
            body: JSON.stringify(payload),
        }));

        saveSession(response.employee);
        userBadge.textContent = `${state.session.employee_name} (${state.session.employee_role})`;
        profilePassword.value = '';
        showAlert(appAlert, 'Profile updated successfully.', 'success');
    } catch (error) {
        showAlert(appAlert, error.message || 'Profile update failed.', 'danger');
    }
}

/**
 * Switches to the tab targeted by dropdown quick actions.
 */
function wireQuickActionMenu() {
    document.querySelectorAll('[data-target-tab]').forEach((button) => {
        button.addEventListener('click', () => {
            const targetId = button.getAttribute('data-target-tab');
            if (!targetId) {
                return;
            }

            const target = document.getElementById(targetId);
            if (target) {
                bootstrap.Tab.getOrCreateInstance(target).show();
            }
        });
    });
}

/**
 * Loads data required by the back office pages.
 */
async function loadApplicationData() {
    const requests = [
        apiRequest('/brands'),
        apiRequest('/categories'),
        apiRequest('/products'),
    ];

    if (state.session && state.session.employee_role !== 'employee') {
        requests.push(apiRequest('/employees'));
    }

    const [brandsPayload, categoriesPayload, productsPayload, employeesPayload] = await Promise.all(requests);

    state.brands = brandsPayload.items || [];
    state.categories = categoriesPayload.items || [];
    state.products = productsPayload.items || [];
    state.employees = employeesPayload?.items || [];

    renderFilters();
    renderProducts(state.products);
    renderEmployees();
}

/**
 * Updates the portal layout based on the authenticated user.
 */
async function startPortal() {
    if (!state.session) {
        return;
    }

    hideAlert(loginAlert);
    hideAlert(appAlert);
    loginSection.classList.add('d-none');
    appSection.classList.remove('d-none');
    userBadge.textContent = `${state.session.employee_name} (${state.session.employee_role})`;
    profileName.value = state.session.employee_name || '';
    profileEmail.value = state.session.employee_email || '';
    newEmployeeStore.value = String(state.session.store_id || '');
    crudStockStoreId.value = String(state.session.store_id || '');

    if (state.session.employee_role === 'employee') {
        employeesTab.classList.add('d-none');
        operationsTab.classList.remove('d-none');
        if (quickEmployeesAction) {
            quickEmployeesAction.classList.add('d-none');
        }
    } else {
        employeesTab.classList.remove('d-none');
        operationsTab.classList.remove('d-none');
        if (quickEmployeesAction) {
            quickEmployeesAction.classList.remove('d-none');
        }
    }

    if (state.session.employee_role === 'employee') {
        employeeCreateForm.closest('.card').classList.add('d-none');
    } else {
        employeeCreateForm.closest('.card').classList.remove('d-none');
    }

    if (state.session.employee_role === 'chief') {
        newEmployeeRole.querySelector('option[value="chief"]').disabled = true;
        newEmployeeStore.disabled = true;
        crudStockStoreId.disabled = true;
    } else if (state.session.employee_role === 'employee') {
        newEmployeeRole.querySelector('option[value="chief"]').disabled = true;
        newEmployeeStore.disabled = true;
        crudStockStoreId.disabled = true;
    } else {
        newEmployeeRole.querySelector('option[value="chief"]').disabled = false;
        newEmployeeStore.disabled = false;
        crudStockStoreId.disabled = false;
    }

    syncCrudFieldsVisibility();
    await loadApplicationData();
}

/**
 * Handles user login from the form.
 *
 * @param {SubmitEvent} event
 */
async function handleLogin(event) {
    event.preventDefault();
    hideAlert(loginAlert);
    loginButton.disabled = true;

    try {
        const formData = new FormData(loginForm);
        const payload = {
            email: String(formData.get('email') || ''),
            password: String(formData.get('password') || ''),
        };

        const response = await apiRequest('/auth/login', {
            method: 'POST',
            body: JSON.stringify(payload),
        });

        saveSession(response.employee);
        await startPortal();
    } catch (error) {
        showAlert(loginAlert, error.message || 'Login failed.', 'danger');
    } finally {
        loginButton.disabled = false;
    }
}

/**
 * Initializes event listeners and attempts session restore.
 */
async function initialize() {
    loginForm.addEventListener('submit', handleLogin);
    logoutButton.addEventListener('click', logout);
    crudForm.addEventListener('submit', handleCrudSubmit);
    crudResource.addEventListener('change', syncCrudFieldsVisibility);
    crudAction.addEventListener('change', syncCrudFieldsVisibility);
    employeeCreateForm.addEventListener('submit', handleEmployeeCreate);
    profileForm.addEventListener('submit', handleProfileUpdate);
    wireQuickActionMenu();
    syncCrudFieldsVisibility();

    applyFiltersButton.addEventListener('click', () => {
        renderProducts(getFilteredProducts());
    });

    resetFiltersButton.addEventListener('click', () => {
        brandFilter.value = '';
        categoryFilter.value = '';
        yearFilter.value = '';
        minPriceFilter.value = '';
        maxPriceFilter.value = '';
        renderProducts(state.products);
    });

    restoreSession();

    if (!state.session) {
        return;
    }

    try {
        const authPayload = await apiRequest('/auth/me');
        saveSession(authPayload.employee);
        await startPortal();
    } catch (error) {
        await logout();
        showAlert(loginAlert, 'Session expired. Please sign in again.', 'warning');
    }
}

initialize();