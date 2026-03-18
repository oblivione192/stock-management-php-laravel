import type { RouteRecordRaw } from 'vue-router';
import CategoriesPage from './pages/CategoriesPage.vue';
import DashboardPage from './pages/DashboardPage.vue';
import HomePage from './pages/HomePage.vue';
import ProductsPage from './pages/ProductsPage.vue';
import StockMovementsPage from './pages/StockMovementsPage.vue';

export const routes: RouteRecordRaw[] = [
    {
        path: '/',
        name: 'home',
        component: HomePage,
    },
    {
        path: '/dashboard',
        name: 'dashboard',
        component: DashboardPage,
    },
    {
        path: '/products',
        name: 'products',
        component: ProductsPage,
    },
    {
        path: '/categories',
        name: 'categories',
        component: CategoriesPage,
    },
    {
        path: '/stock-movements',
        name: 'stock-movements',
        component: StockMovementsPage,
    },
];
