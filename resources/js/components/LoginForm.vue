<script setup>
import { inject, reactive, ref } from 'vue';

const appName = inject('appName', 'Daily Management');
const loginUrl = inject('loginUrl', 'login');
const dashboardUrl = inject('dashboardUrl', 'dashboard');

const form = reactive({
    username: '',
    password: '',
    remember: false,
});

const errors = ref({});
const message = ref('');
const loading = ref(false);

const submit = async () => {
    loading.value = true;
    errors.value = {};
    message.value = '';

    try {
        const { data } = await window.axios.post(loginUrl, {
            username: form.username,
            password: form.password,
            remember: form.remember,
        });

        window.location.href = data.redirect ?? dashboardUrl;
    } catch (error) {
        if (error.response?.status === 422) {
            errors.value = error.response.data.errors ?? {};
            message.value = error.response.data.message ?? '入力内容を確認してください。';
        } else {
            message.value = 'ログインに失敗しました。しばらくしてから再度お試しください。';
        }
    } finally {
        loading.value = false;
    }
};
</script>

<template>
    <div class="login-page">
        <div class="login-backdrop" aria-hidden="true"></div>

        <main class="login-shell">
            <section class="login-panel" :class="{ 'is-ready': true }">
                <header class="login-header">
                    <p class="login-brand">{{ appName }}</p>
                    <h1 class="login-title">ログイン</h1>
                    <p class="login-lead">日次業務の記録と確認を始めます。</p>
                </header>

                <form class="login-form" @submit.prevent="submit" novalidate>
                    <div v-if="message" class="login-alert" role="alert">
                        {{ message }}
                    </div>

                    <label class="login-field">
                        <span>ユーザー名</span>
                        <input
                            v-model="form.username"
                            type="text"
                            name="username"
                            autocomplete="username"
                            required
                            :disabled="loading"
                            :aria-invalid="Boolean(errors.username)"
                        />
                        <small v-if="errors.username">{{ errors.username[0] }}</small>
                    </label>

                    <label class="login-field">
                        <span>パスワード</span>
                        <input
                            v-model="form.password"
                            type="password"
                            name="password"
                            autocomplete="current-password"
                            required
                            :disabled="loading"
                            :aria-invalid="Boolean(errors.password)"
                        />
                        <small v-if="errors.password">{{ errors.password[0] }}</small>
                    </label>

                    <label class="login-remember">
                        <input v-model="form.remember" type="checkbox" :disabled="loading" />
                        <span>ログイン状態を保持する</span>
                    </label>

                    <button class="login-submit" type="submit" :disabled="loading">
                        {{ loading ? 'ログイン中…' : 'ログイン' }}
                    </button>
                </form>
            </section>
        </main>
    </div>
</template>

<style scoped>
.login-page {
    position: relative;
    min-height: 100vh;
    overflow: hidden;
    color: var(--color-ink);
    background: var(--color-surface);
}

.login-backdrop {
    position: absolute;
    inset: 0;
    background:
        radial-gradient(circle at 12% 18%, rgba(15, 118, 110, 0.18), transparent 42%),
        radial-gradient(circle at 88% 12%, rgba(28, 25, 23, 0.08), transparent 36%),
        linear-gradient(160deg, #f8faf9 0%, #eef2f1 48%, #e7eeec 100%);
}

.login-backdrop::after {
    content: '';
    position: absolute;
    inset: auto -10% -20% 40%;
    height: 55%;
    background:
        repeating-linear-gradient(
            -18deg,
            transparent 0 18px,
            rgba(15, 118, 110, 0.05) 18px 19px
        );
    transform: rotate(-6deg);
    pointer-events: none;
}

.login-shell {
    position: relative;
    z-index: 1;
    display: grid;
    place-items: center;
    min-height: 100vh;
    padding: 1.5rem;
}

.login-panel {
    width: min(100%, 26rem);
    padding: 2rem;
    border: 1px solid var(--color-line);
    border-radius: 1rem;
    background: color-mix(in srgb, var(--color-panel) 92%, transparent);
    backdrop-filter: blur(8px);
    box-shadow: 0 18px 40px rgba(28, 25, 23, 0.08);
    opacity: 0;
    transform: translateY(12px);
    animation: rise-in 0.55s ease forwards;
}

.login-header {
    margin-bottom: 1.75rem;
}

.login-brand {
    margin: 0 0 0.75rem;
    color: var(--color-brand);
    font-size: 0.8rem;
    font-weight: 600;
    letter-spacing: 0.08em;
    text-transform: uppercase;
}

.login-title {
    margin: 0;
    font-size: 1.75rem;
    font-weight: 600;
    letter-spacing: -0.02em;
}

.login-lead {
    margin: 0.5rem 0 0;
    color: var(--color-muted);
    font-size: 0.95rem;
}

.login-form {
    display: grid;
    gap: 1rem;
}

.login-alert {
    padding: 0.75rem 0.9rem;
    border: 1px solid color-mix(in srgb, var(--color-danger) 25%, white);
    border-radius: 0.6rem;
    background: color-mix(in srgb, var(--color-danger) 8%, white);
    color: var(--color-danger);
    font-size: 0.875rem;
}

.login-field {
    display: grid;
    gap: 0.4rem;
    font-size: 0.875rem;
    font-weight: 500;
}

.login-field input {
    width: 100%;
    padding: 0.75rem 0.9rem;
    border: 1px solid var(--color-line);
    border-radius: 0.65rem;
    background: #fff;
    color: var(--color-ink);
    outline: none;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.login-field input:focus {
    border-color: var(--color-brand);
    box-shadow: 0 0 0 3px rgba(15, 118, 110, 0.15);
}

.login-field input[aria-invalid='true'] {
    border-color: var(--color-danger);
}

.login-field small {
    color: var(--color-danger);
    font-weight: 400;
}

.login-remember {
    display: flex;
    align-items: center;
    gap: 0.55rem;
    color: var(--color-muted);
    font-size: 0.875rem;
}

.login-remember input {
    width: 1rem;
    height: 1rem;
    accent-color: var(--color-brand);
}

.login-submit {
    margin-top: 0.35rem;
    width: 100%;
    padding: 0.85rem 1rem;
    border: 0;
    border-radius: 0.7rem;
    background: var(--color-brand);
    color: #fff;
    font-weight: 600;
    cursor: pointer;
    transition: background-color 0.2s ease, transform 0.2s ease;
}

.login-submit:hover:not(:disabled) {
    background: var(--color-brand-dark);
    transform: translateY(-1px);
}

.login-submit:disabled {
    opacity: 0.7;
    cursor: not-allowed;
}

@keyframes rise-in {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@media (max-width: 480px) {
    .login-panel {
        padding: 1.5rem;
    }

    .login-title {
        font-size: 1.5rem;
    }
}
</style>
