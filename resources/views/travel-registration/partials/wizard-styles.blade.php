<style>
    #travel-registration-wizard.wizard > .steps {
        display: none !important;
    }

    #travel-registration-wizard.wizard > .content {
        min-height: 200px;
        margin-top: 0;
        padding-top: 0;
    }

    #travel-registration-wizard.wizard > .actions {
        margin-top: 1.5rem;
        padding-top: 1rem;
        border-top: 1px solid var(--bs-border-color);
    }

    #travel-registration-wizard.wizard > .actions > ul {
        display: flex;
        justify-content: space-between;
        width: 100%;
    }

    #travel-registration-wizard.wizard > .actions > ul > li > a {
        min-width: 110px;
        text-align: center;
    }

    .travel-wizard-steps {
        position: relative;
        padding: 0;
    }

    .travel-wizard-steps::before {
        content: '';
        position: absolute;
        top: 16px;
        left: 5%;
        right: 5%;
        height: 2px;
        background: var(--bs-border-color);
        z-index: 0;
    }

    .travel-wizard-step {
        position: relative;
        z-index: 1;
        min-width: 0;
        padding: 0 2px;
    }

    .travel-wizard-step-marker {
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

    .travel-wizard-step-check {
        display: none;
        font-size: 16px;
    }

    .travel-wizard-step-label {
        margin-top: 8px;
        font-size: 10px;
        line-height: 1.2;
        color: var(--bs-secondary-color);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        padding: 0 2px;
    }

    .travel-wizard-step.is-active .travel-wizard-step-marker {
        border-color: var(--bs-primary);
        background: var(--bs-primary);
        color: #fff;
    }

    .travel-wizard-step.is-active .travel-wizard-step-label {
        color: var(--bs-primary);
        font-weight: 600;
    }

    .travel-wizard-step.is-done .travel-wizard-step-marker {
        border-color: var(--bs-success);
        background: var(--bs-success);
        color: #fff;
    }

    .travel-wizard-step.is-done .travel-wizard-step-number {
        display: none;
    }

    .travel-wizard-step.is-done .travel-wizard-step-check {
        display: inline-block;
    }

    .travel-wizard-step.is-done .travel-wizard-step-label {
        color: var(--bs-success);
    }

    .custom-wizard-error,
    .validation-error-message {
        display: block !important;
    }

    @media (max-width: 767.98px) {
        .travel-wizard-progress {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            padding-bottom: 4px;
        }

        .travel-wizard-steps {
            min-width: 480px;
        }

        .travel-wizard-step-marker {
            width: 28px;
            height: 28px;
            font-size: 11px;
        }

        .travel-wizard-steps::before {
            top: 14px;
        }
    }
</style>
