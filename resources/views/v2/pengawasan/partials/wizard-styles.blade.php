<style>
    .pengawasan-wizard-panel {
        display: none;
    }

    .pengawasan-wizard-panel.is-active {
        display: block;
    }

    .pengawasan-wizard-steps {
        position: relative;
        padding: 0;
    }

    .pengawasan-wizard-steps::before {
        content: '';
        position: absolute;
        top: 16px;
        left: 8%;
        right: 8%;
        height: 2px;
        background: var(--bs-border-color);
        z-index: 0;
    }

    .pengawasan-wizard-step {
        position: relative;
        z-index: 1;
        min-width: 0;
        padding: 0 2px;
    }

    .pengawasan-wizard-step-marker {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 12px;
        background: var(--bs-body-bg);
        border: 2px solid var(--bs-border-color);
        color: var(--bs-secondary-color);
    }

    .pengawasan-wizard-step-check {
        display: none;
        font-size: 16px;
    }

    .pengawasan-wizard-step-label {
        margin-top: 8px;
        font-size: 11px;
        line-height: 1.2;
        color: var(--bs-secondary-color);
    }

    .pengawasan-wizard-step.is-active .pengawasan-wizard-step-marker {
        border-color: var(--bs-primary);
        background: var(--bs-primary);
        color: #fff;
    }

    .pengawasan-wizard-step.is-active .pengawasan-wizard-step-label {
        color: var(--bs-primary);
        font-weight: 600;
    }

    .pengawasan-wizard-step.is-done .pengawasan-wizard-step-marker {
        border-color: var(--bs-success);
        background: var(--bs-success);
        color: #fff;
    }

    .pengawasan-wizard-step.is-done .pengawasan-wizard-step-number {
        display: none;
    }

    .pengawasan-wizard-step.is-done .pengawasan-wizard-step-check {
        display: inline-block;
    }

    .pengawasan-wizard-step.is-done .pengawasan-wizard-step-label {
        color: var(--bs-success);
    }

    .pengawasan-wizard-step.is-clickable {
        cursor: pointer;
    }

    .pengawasan-wizard-actions {
        margin-top: 1.5rem;
        padding-top: 1rem;
        border-top: 1px solid var(--bs-border-color);
        display: flex;
        justify-content: space-between;
        gap: 0.75rem;
        flex-wrap: wrap;
    }

    .pengawasan-checklist-item {
        border: 1px solid var(--bs-border-color);
        border-radius: var(--bs-border-radius);
        padding: 1rem;
        margin-bottom: 1rem;
    }

    .pengawasan-checklist-item:last-child {
        margin-bottom: 0;
    }

    @media (max-width: 767.98px) {
        .pengawasan-wizard-progress {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            padding-bottom: 4px;
        }

        .pengawasan-wizard-steps {
            min-width: 320px;
        }

        .pengawasan-wizard-step-marker {
            width: 28px;
            height: 28px;
            font-size: 11px;
        }

        .pengawasan-wizard-steps::before {
            top: 14px;
        }

        .pengawasan-wizard-step-label {
            font-size: 10px;
        }
    }
</style>
