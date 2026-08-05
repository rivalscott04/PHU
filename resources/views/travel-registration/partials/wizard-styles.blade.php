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
        border-top: 1px solid #eef1f5;
    }

    #travel-registration-wizard.wizard > .actions > ul {
        display: flex;
        justify-content: space-between;
        width: 100%;
    }

    #travel-registration-wizard.wizard > .actions > ul > li > a {
        min-width: 110px;
        text-align: center;
        padding: 0.55rem 1.25rem;
        border-radius: 8px;
        font-weight: 600;
    }

    #travel-registration-wizard.wizard > .actions > ul > li:last-child > a {
        background: #556ee6;
        color: #fff;
    }

    .travel-wizard-card {
        border: none;
        box-shadow: 0 4px 24px rgba(18, 38, 63, 0.08);
        border-radius: 16px;
    }

    .travel-wizard-steps {
        position: relative;
        padding: 0;
        gap: 0;
    }

    .travel-wizard-steps::before {
        content: '';
        position: absolute;
        top: 16px;
        left: 5%;
        right: 5%;
        height: 2px;
        background: var(--bs-border-color, #dee2e6);
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
        background: #fff;
        border: 2px solid var(--bs-border-color, #dee2e6);
        color: var(--bs-secondary-color, #6c757d);
    }

    .travel-wizard-step-check {
        display: none;
        font-size: 16px;
    }

    .travel-wizard-step-label {
        margin-top: 8px;
        font-size: 10px;
        line-height: 1.2;
        color: var(--bs-secondary-color, #6c757d);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        padding: 0 2px;
    }

    .travel-wizard-step.is-active .travel-wizard-step-marker {
        border-color: var(--bs-primary, #556ee6);
        background: var(--bs-primary, #556ee6);
        color: #fff;
    }

    .travel-wizard-step.is-active .travel-wizard-step-label {
        color: var(--bs-primary, #556ee6);
        font-weight: 600;
    }

    .travel-wizard-step.is-done .travel-wizard-step-marker {
        border-color: var(--bs-success, #34c38f);
        background: var(--bs-success, #34c38f);
        color: #fff;
    }

    .travel-wizard-step.is-done .travel-wizard-step-number {
        display: none;
    }

    .travel-wizard-step.is-done .travel-wizard-step-check {
        display: inline-block;
    }

    .travel-wizard-step.is-done .travel-wizard-step-label {
        color: var(--bs-success, #34c38f);
    }

    .travel-wizard-current-label {
        text-align: center;
        font-weight: 500;
    }

    .travel-step-intro {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        padding: 16px 18px;
        background: #f8f9ff;
        border-radius: 12px;
        border: 1px solid #eef1ff;
        max-width: 640px;
        margin-left: auto;
        margin-right: auto;
    }

    .travel-step-intro-icon {
        flex-shrink: 0;
        width: 42px;
        height: 42px;
        border-radius: 10px;
        background: #556ee6;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }

    .travel-step-intro-title {
        font-size: 16px;
        font-weight: 600;
        color: #343a40;
    }

    .travel-step-intro-desc {
        font-size: 13px;
        color: #74788d;
        line-height: 1.5;
    }

    .travel-form-fields .form-label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 6px;
    }

    .travel-form-fields .form-text {
        font-size: 12px;
        color: #74788d;
    }

    .travel-upload-card {
        padding: 20px;
        border: 2px dashed #d6daf0;
        border-radius: 12px;
        background: #fafbff;
        text-align: center;
        transition: border-color 0.2s;
    }

    .travel-upload-card:hover {
        border-color: #556ee6;
    }

    .travel-upload-card-icon {
        width: 48px;
        height: 48px;
        margin: 0 auto 12px;
        border-radius: 50%;
        background: #eef1ff;
        color: #556ee6;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
    }

    .travel-upload-card input[type="file"] {
        max-width: 100%;
        margin: 0 auto;
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

        .travel-step-intro {
            flex-direction: column;
            text-align: center;
            align-items: center;
        }
    }

    .validation-error {
        animation: travelWizardShake 0.5s ease-in-out;
    }

    .validation-error .form-control,
    .validation-error .form-select {
        border-color: #f46a6a !important;
        box-shadow: 0 0 0 0.2rem rgba(244, 106, 106, 0.25) !important;
    }

    .validation-error .form-label {
        color: #f46a6a !important;
    }

    .validation-error-message,
    .custom-wizard-error {
        display: block !important;
        font-size: 12px;
        font-weight: 500;
        color: #f46a6a;
        margin-top: 4px;
    }

    .form-control.is-invalid,
    .form-select.is-invalid {
        border-color: #f46a6a !important;
        box-shadow: 0 0 0 0.2rem rgba(244, 106, 106, 0.25) !important;
    }

    .form-control.is-valid,
    .form-select.is-valid {
        border-color: #34c38f !important;
        box-shadow: 0 0 0 0.2rem rgba(52, 195, 143, 0.25) !important;
    }

    .travel-review-group {
        border: 1px solid #e9ecef;
        border-radius: 12px;
        padding: 18px;
        background: #fff;
    }

    .travel-review-group-title {
        color: #556ee6;
        font-weight: 600;
        margin-bottom: 12px;
        padding-bottom: 8px;
        border-bottom: 1px solid #eef1ff;
        font-size: 14px;
    }

    .travel-review-label {
        font-size: 12px;
        color: #74788d;
        margin-bottom: 2px;
    }

    .travel-review-value {
        font-size: 14px;
        font-weight: 600;
        color: #495057;
        word-break: break-word;
    }

    @keyframes travelWizardShake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }
</style>
