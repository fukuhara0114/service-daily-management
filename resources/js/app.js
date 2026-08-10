import 'temporal-polyfill/global';
import './bootstrap';
import '../css/app.css';

import { createApp } from 'vue';
import LoginForm from './components/LoginForm.vue';
import ActivityCalendar from './components/ActivityCalendar.vue';

const el = document.getElementById('app');

if (!el) {
    // no-op
} else if (el.dataset.page === 'login') {
    createApp(LoginForm)
        .provide('appName', el.dataset.appName ?? 'Daily Management')
        .provide('loginUrl', el.dataset.loginUrl ?? 'login')
        .provide('dashboardUrl', el.dataset.dashboardUrl ?? 'dashboard')
        .mount(el);
} else if (el.dataset.page === 'dashboard') {
    createApp(ActivityCalendar)
        .provide('appName', el.dataset.appName ?? 'Daily Management')
        .provide('userName', el.dataset.userName ?? '')
        .provide('activitiesUrl', el.dataset.activitiesUrl ?? '/daily-activities')
        .provide('upsertUrl', el.dataset.upsertUrl ?? '/daily-activities')
        .mount(el);
}
