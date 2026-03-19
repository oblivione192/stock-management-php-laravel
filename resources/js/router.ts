import type { RouteRecordRaw } from 'vue-router';
import AuthPage from './pages/AuthPage.vue';
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
        meta: {
            requiresAuth: true,
        },
    },
    {
        path: '/auth',
        name: 'auth',
        component: AuthPage,
        meta: {
            guestOnly: true,
            hideHeader: true,
        },
    },
    {
        path: '/dashboard',
        name: 'dashboard',
        component: DashboardPage,
        meta: {
            requiresAuth: true,
        },
    },
    {
        path: '/products',
        name: 'products',
        component: ProductsPage,
        meta: {
            requiresAuth: true,
        },
    },
    {
        path: '/categories',
        name: 'categories',
        component: CategoriesPage,
        meta: {
            requiresAuth: true,
        },
    },
    {
        path: '/stock-movements',
        name: 'stock-movements',
        component: StockMovementsPage,
        meta: {
            requiresAuth: true,
        },
    },
];
