import { createRouter, createWebHistory } from "vue-router";

export default createRouter({
    history: createWebHistory(),
    routes: [
        {
            path: '/',
            redirect: {name: 'home'},
        },
        {
            path: '/home',
            name: 'home',
            component: () => import('./components/Home.vue'),
        },
        {
            path: '/login',
            name: 'login',
            component: () => import('./components/Login.vue'),
        },
        {
            path: '/registration',
            name: 'registration',
            component: () => import('./components/Registration.vue'),
        },
        {
            path: '/user/:id',
            name: 'user',
            component: () => import('./components/User.vue'),
        }
    ]
})
