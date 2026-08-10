<script setup>
import { computed, inject, reactive, ref } from 'vue';
import FullCalendar from '@fullcalendar/vue3';
import classicThemePlugin from '@fullcalendar/vue3/themes/classic';
import dayGridPlugin from '@fullcalendar/vue3/daygrid';
import interactionPlugin from '@fullcalendar/vue3/interaction';
import jaLocale from '@fullcalendar/vue3/locales/ja';

import '@fullcalendar/vue3/skeleton.css';
import '@fullcalendar/vue3/themes/classic/theme.css';
import '@fullcalendar/vue3/themes/classic/palette.css';

const appName = inject('appName', 'Daily Management');
const userName = inject('userName', '');
const activitiesUrl = inject('activitiesUrl', '/daily-activities');
const upsertUrl = inject('upsertUrl', '/daily-activities');

const calendarRef = ref(null);
const modalOpen = ref(false);
const saving = ref(false);
const errorMessage = ref('');

const form = reactive({
    work_date: '',
    new_case_count: 0,
    mail_notice_count: 0,
    remarks: '',
});

const modalTitle = computed(() => {
    if (!form.work_date) {
        return '日次記録';
    }

    return `${form.work_date} の記録`;
});

const openModal = (date, props = {}) => {
    form.work_date = date;
    form.new_case_count = Number(props.new_case_count ?? 0);
    form.mail_notice_count = Number(props.mail_notice_count ?? 0);
    form.remarks = props.remarks ?? '';
    errorMessage.value = '';
    modalOpen.value = true;
};

const closeModal = () => {
    if (saving.value) {
        return;
    }

    modalOpen.value = false;
};

const fetchEvents = async (info, successCallback, failureCallback) => {
    try {
        const { data } = await window.axios.get(activitiesUrl, {
            params: {
                start: info.startStr,
                end: info.endStr,
            },
        });
        successCallback(data);
    } catch (error) {
        failureCallback(error);
    }
};

const handleDateClick = (info) => {
    openModal(info.dateStr);
};

const handleEventClick = (info) => {
    openModal(info.event.startStr, info.event.extendedProps);
};

const save = async () => {
    saving.value = true;
    errorMessage.value = '';

    try {
        await window.axios.post(upsertUrl, {
            work_date: form.work_date,
            new_case_count: Number(form.new_case_count),
            mail_notice_count: Number(form.mail_notice_count),
            remarks: form.remarks || null,
        });

        calendarRef.value?.getApi()?.refetchEvents();
        modalOpen.value = false;
    } catch (error) {
        if (error.response?.status === 422) {
            const errors = error.response.data.errors ?? {};
            errorMessage.value =
                Object.values(errors).flat()[0] ??
                error.response.data.message ??
                '入力内容を確認してください。';
        } else {
            errorMessage.value = '保存に失敗しました。しばらくしてから再度お試しください。';
        }
    } finally {
        saving.value = false;
    }
};

const calendarOptions = {
    plugins: [classicThemePlugin, dayGridPlugin, interactionPlugin],
    initialView: 'dayGridMonth',
    locale: jaLocale,
    height: 'auto',
    headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: '',
    },
    buttonText: {
        today: '今日',
    },
    editable: false,
    selectable: false,
    dayMaxEvents: true,
    events: fetchEvents,
    dateClick: handleDateClick,
    eventClick: handleEventClick,
};
</script>

<template>
    <div class="activity-page">
        <header class="activity-header">
            <div>
                <p class="activity-brand">{{ appName }}</p>
                <h1 class="activity-title">日次記録</h1>
                <p class="activity-lead">{{ userName }} さんの保守案件・案内メール記録</p>
            </div>
        </header>

        <section class="activity-calendar-panel">
            <FullCalendar ref="calendarRef" :options="calendarOptions" />
        </section>

        <div
            v-if="modalOpen"
            class="activity-modal-backdrop"
            @click.self="closeModal"
        >
            <div class="activity-modal" role="dialog" aria-modal="true" :aria-label="modalTitle">
                <header class="activity-modal-header">
                    <h2>{{ modalTitle }}</h2>
                    <button type="button" class="activity-modal-close" @click="closeModal" :disabled="saving">
                        ×
                    </button>
                </header>

                <form class="activity-form" @submit.prevent="save">
                    <p v-if="errorMessage" class="activity-alert" role="alert">{{ errorMessage }}</p>

                    <label class="activity-field">
                        <span>新規保守案件登録件数</span>
                        <input
                            v-model.number="form.new_case_count"
                            type="number"
                            min="0"
                            step="1"
                            required
                            :disabled="saving"
                        />
                    </label>

                    <label class="activity-field">
                        <span>保守案件メール案内件数</span>
                        <input
                            v-model.number="form.mail_notice_count"
                            type="number"
                            min="0"
                            step="1"
                            required
                            :disabled="saving"
                        />
                    </label>

                    <label class="activity-field">
                        <span>備考</span>
                        <textarea
                            v-model="form.remarks"
                            rows="3"
                            :disabled="saving"
                        ></textarea>
                    </label>

                    <div class="activity-actions">
                        <button type="button" class="activity-btn-secondary" @click="closeModal" :disabled="saving">
                            キャンセル
                        </button>
                        <button type="submit" class="activity-btn-primary" :disabled="saving">
                            {{ saving ? '保存中…' : '保存' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<style scoped>
.activity-page {
    display: grid;
    gap: 1.5rem;
}

.activity-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 1rem;
}

.activity-brand {
    margin: 0;
    color: var(--color-brand);
    font-size: 0.8rem;
    font-weight: 600;
    letter-spacing: 0.08em;
    text-transform: uppercase;
}

.activity-title {
    margin: 0.35rem 0 0;
    font-size: 1.75rem;
    font-weight: 600;
    letter-spacing: -0.02em;
}

.activity-lead {
    margin: 0.4rem 0 0;
    color: var(--color-muted);
    font-size: 0.95rem;
}

.activity-calendar-panel {
    padding: 1rem;
    border: 1px solid var(--color-line);
    border-radius: 1rem;
    background: var(--color-panel);
    box-shadow: 0 10px 30px rgba(28, 25, 23, 0.05);
}

.activity-calendar-panel :deep(.fc) {
    --fc-border-color: var(--color-line);
    --fc-button-bg-color: var(--color-brand);
    --fc-button-border-color: var(--color-brand);
    --fc-button-hover-bg-color: var(--color-brand-dark);
    --fc-button-hover-border-color: var(--color-brand-dark);
    --fc-button-active-bg-color: var(--color-brand-dark);
    --fc-button-active-border-color: var(--color-brand-dark);
    --fc-today-bg-color: color-mix(in srgb, var(--color-brand) 10%, white);
    --fc-event-bg-color: var(--color-brand);
    --fc-event-border-color: var(--color-brand);
    --fc-page-bg-color: transparent;
}

.activity-calendar-panel :deep(.fc .fc-toolbar-title) {
    font-size: 1.2rem;
    font-weight: 600;
}

.activity-calendar-panel :deep(.fc .fc-daygrid-day) {
    cursor: pointer;
}

.activity-calendar-panel :deep(.fc .fc-event) {
    cursor: pointer;
    font-size: 0.75rem;
    padding: 0.1rem 0.25rem;
}

.activity-modal-backdrop {
    position: fixed;
    inset: 0;
    z-index: 40;
    display: grid;
    place-items: center;
    padding: 1rem;
    background: rgba(28, 25, 23, 0.35);
}

.activity-modal {
    width: min(100%, 28rem);
    border-radius: 1rem;
    background: var(--color-panel);
    box-shadow: 0 24px 48px rgba(28, 25, 23, 0.18);
    animation: modal-in 0.2s ease;
}

.activity-modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    padding: 1.1rem 1.25rem 0.75rem;
}

.activity-modal-header h2 {
    margin: 0;
    font-size: 1.15rem;
    font-weight: 600;
}

.activity-modal-close {
    border: 0;
    background: transparent;
    color: var(--color-muted);
    font-size: 1.5rem;
    line-height: 1;
    cursor: pointer;
}

.activity-form {
    display: grid;
    gap: 1rem;
    padding: 0 1.25rem 1.25rem;
}

.activity-alert {
    margin: 0;
    padding: 0.7rem 0.85rem;
    border: 1px solid color-mix(in srgb, var(--color-danger) 25%, white);
    border-radius: 0.6rem;
    background: color-mix(in srgb, var(--color-danger) 8%, white);
    color: var(--color-danger);
    font-size: 0.875rem;
}

.activity-field {
    display: grid;
    gap: 0.4rem;
    font-size: 0.875rem;
    font-weight: 500;
}

.activity-field input,
.activity-field textarea {
    width: 100%;
    padding: 0.7rem 0.85rem;
    border: 1px solid var(--color-line);
    border-radius: 0.65rem;
    background: #fff;
    color: var(--color-ink);
    outline: none;
    resize: vertical;
}

.activity-field input:focus,
.activity-field textarea:focus {
    border-color: var(--color-brand);
    box-shadow: 0 0 0 3px rgba(15, 118, 110, 0.15);
}

.activity-actions {
    display: flex;
    justify-content: flex-end;
    gap: 0.65rem;
    margin-top: 0.25rem;
}

.activity-btn-primary,
.activity-btn-secondary {
    border-radius: 0.65rem;
    padding: 0.7rem 1rem;
    font-weight: 600;
    cursor: pointer;
}

.activity-btn-primary {
    border: 0;
    background: var(--color-brand);
    color: #fff;
}

.activity-btn-primary:hover:not(:disabled) {
    background: var(--color-brand-dark);
}

.activity-btn-secondary {
    border: 1px solid var(--color-line);
    background: #fff;
    color: var(--color-ink);
}

.activity-btn-primary:disabled,
.activity-btn-secondary:disabled {
    opacity: 0.7;
    cursor: not-allowed;
}

@keyframes modal-in {
    from {
        opacity: 0;
        transform: translateY(8px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@media (max-width: 640px) {
    .activity-header {
        flex-direction: column;
    }

    .activity-title {
        font-size: 1.45rem;
    }

    .activity-calendar-panel {
        padding: 0.75rem;
    }
}
</style>
