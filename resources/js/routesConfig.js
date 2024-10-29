// routesConfig.js
const isProduction = process.env.NODE_ENV === 'production';

const baseUrl = isProduction ? 'http://3.20.227.233/AppPedidos' : 'http://127.0.0.1:8000';

const routes = {
    customersSearch: `${baseUrl}/customers/search`,
    productsSearch: `${baseUrl}/products/search`,
    getMunicipalities: `${baseUrl}/get-municipalities`,
    productsStore: `${baseUrl}/products/store`,
    getProducts: `${baseUrl}/products/get`,
};


export default routes;