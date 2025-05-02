import { createRouter, createWebHistory } from 'vue-router';

// Layouts
import PublicLayout from '@/layouts/PublicLayout.vue';
import AdminLayout from '@/layouts/AdminLayout.vue';

// Public Views
import Home from '@/views/Home.vue';
import Events from '@/views/Events.vue';
import EventDetail from '@/views/EventDetail.vue';

// Admin Views
import Login from '@/views/admin/Login.vue';
import Dashboard from '@/views/admin/Dashboard.vue';
import AdminEvents from '@/views/admin/Events.vue';
import EventCreate from '@/views/admin/EventCreate.vue';
import EventEdit from '@/views/admin/EventEdit.vue';
import Participants from '@/views/admin/Participants.vue';
import Statistics from '@/views/admin/Statistics.vue';

const routes = [
    {
        path: '/',
        component: PublicLayout,
        children: [
            {
                path: '',
                name: 'home',
                component: Home
            },
            {
                path: 'events',
                name: 'events',
                component: Events
            },
            {
                path: 'events/:id',
                name: 'event-detail',
                component: EventDetail,
                props: true
            }
        ]
    },
    {
        path: '/admin/login',
        name: 'login',
        component: Login
    },
    {
        path: '/admin',
        component: AdminLayout,
        meta: { requiresAuth: true },
        children: [
            {
                path: 'dashboard',
                name: 'dashboard',
                component: Dashboard
            },
            {
                path: 'events',
                name: 'admin-events',
                component: AdminEvents
            },
            {
                path: 'events/create',
                name: 'event-create',
                component: EventCreate
            },
            {
                path: 'events/:id/edit',
                name: 'event-edit',
                component: EventEdit,
                props: true
            },
            {
                path: 'events/:id/participants',
                name: 'event-participants',
                component: Participants,
                props: true
            },
            {
                path: 'statistics',
                name: 'statistics',
                component: Statistics
            }
        ]
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

router.beforeEach((to, from, next) => {
    const token = localStorage.getItem('token');

    if (to.matched.some(record => record.meta.requiresAuth)) {
        if (!token) {
            next({ name: 'login' });
        } else {
            next();
        }
    } else {
        next();
    }
});

export default router;
