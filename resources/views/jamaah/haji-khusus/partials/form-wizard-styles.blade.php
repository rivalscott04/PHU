@once
    @push('styles')
        <style>
            #jamaah-haji-khusus-wizard.wizard > .steps {
                display: none !important;
            }

            #jamaah-haji-khusus-wizard .row.g-3 > [class*="col-"] > .mb-3 {
                margin-bottom: 0 !important;
            }

            #jamaah-haji-khusus-wizard .form-label .text-danger {
                font-weight: 700;
            }

            #jamaah-haji-khusus-wizard.wizard > .content {
                min-height: 200px;
                margin-top: 0;
                padding-top: 0;
            }

            #jamaah-haji-khusus-wizard.wizard > .actions {
                margin-top: 1rem;
            }

            #jamaah-haji-khusus-wizard.wizard > .actions > ul > li > a {
                min-width: 96px;
                text-align: center;
            }

            .jamaah-wizard-steps {
                position: relative;
                padding: 0;
                gap: 0;
            }

            .jamaah-wizard-steps::before {
                content: '';
                position: absolute;
                top: 16px;
                left: 7%;
                right: 7%;
                height: 2px;
                background: var(--bs-border-color, #dee2e6);
                z-index: 0;
            }

            .jamaah-wizard-step {
                position: relative;
                z-index: 1;
                min-width: 0;
                padding: 0 2px;
            }

            .jamaah-wizard-step-marker {
                width: 32px;
                height: 32px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: 600;
                font-size: 13px;
                background: #fff;
                border: 2px solid var(--bs-border-color, #dee2e6);
                color: var(--bs-secondary-color, #6c757d);
            }

            .jamaah-wizard-step-check {
                display: none;
                font-size: 16px;
            }

            .jamaah-wizard-step-label {
                margin-top: 8px;
                font-size: 11px;
                line-height: 1.2;
                color: var(--bs-secondary-color, #6c757d);
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                padding: 0 2px;
            }

            .jamaah-wizard-step.is-active .jamaah-wizard-step-marker {
                border-color: var(--bs-primary, #556ee6);
                background: var(--bs-primary, #556ee6);
                color: #fff;
            }

            .jamaah-wizard-step.is-active .jamaah-wizard-step-label {
                color: var(--bs-primary, #556ee6);
                font-weight: 600;
            }

            .jamaah-wizard-step.is-done .jamaah-wizard-step-marker {
                border-color: var(--bs-success, #34c38f);
                background: var(--bs-success, #34c38f);
                color: #fff;
            }

            .jamaah-wizard-step.is-done .jamaah-wizard-step-number {
                display: none;
            }

            .jamaah-wizard-step.is-done .jamaah-wizard-step-check {
                display: inline-block;
            }

            .jamaah-wizard-step.is-done .jamaah-wizard-step-label {
                color: var(--bs-success, #34c38f);
            }

            @media (max-width: 767.98px) {
                .jamaah-wizard-progress {
                    overflow-x: auto;
                    -webkit-overflow-scrolling: touch;
                    padding-bottom: 4px;
                }

                .jamaah-wizard-steps {
                    min-width: 560px;
                }

                .jamaah-wizard-step-marker {
                    width: 28px;
                    height: 28px;
                    font-size: 12px;
                }

                .jamaah-wizard-steps::before {
                    top: 14px;
                }

                .jamaah-wizard-step-label {
                    font-size: 10px;
                }
            }

            .form-control.digits-only-blocked,
            .form-select.digits-only-blocked {
                border-color: #f1b44c !important;
                box-shadow: 0 0 0 0.2rem rgba(241, 180, 76, 0.2) !important;
            }

            .digits-only-hint {
                display: block;
                font-size: 12px;
                font-weight: 500;
                color: #e8a317;
                margin-top: 4px;
                opacity: 0;
                transform: translateY(-4px);
                transition: opacity 0.2s ease, transform 0.2s ease;
            }

            .digits-only-hint.is-visible {
                opacity: 1;
                transform: translateY(0);
            }

            .validation-error {
                animation: shake 0.5s ease-in-out;
            }

            .validation-error .form-control,
            .validation-error .form-select {
                border-color: #f46a6a !important;
                box-shadow: 0 0 0 0.2rem rgba(244, 106, 106, 0.25) !important;
            }

            .validation-error .form-label {
                color: #f46a6a !important;
                font-weight: 700;
            }

            .validation-error-message,
            .custom-wizard-error {
                display: block !important;
                font-size: 12px;
                font-weight: 500;
                color: #f46a6a;
                margin-top: 4px;
                animation: fadeIn 0.3s ease-in-out;
            }

            .validation-error .form-control,
            .validation-error .form-select,
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

            #validation-summary {
                border: none;
                border-radius: 12px;
                box-shadow: 0 4px 12px rgba(244, 106, 106, 0.15);
                animation: slideInDown 0.5s ease-out;
            }

            #validation-summary ul {
                list-style: none;
                padding-left: 0;
                margin-bottom: 0;
            }

            #validation-summary li {
                padding: 4px 0;
                font-size: 14px;
                color: #721c24;
                position: relative;
                padding-left: 20px;
            }

            #validation-summary li:before {
                content: "•";
                color: #f46a6a;
                font-weight: bold;
                position: absolute;
                left: 0;
                top: 2px;
            }

            @keyframes shake {
                0%, 100% { transform: translateX(0); }
                25% { transform: translateX(-5px); }
                75% { transform: translateX(5px); }
            }

            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(-10px); }
                to { opacity: 1; transform: translateY(0); }
            }

            @keyframes slideInDown {
                from { opacity: 0; transform: translateY(-20px); }
                to { opacity: 1; transform: translateY(0); }
            }

            .location-select {
                border: 2px solid #e9ecef;
                border-radius: 8px;
                padding: 12px 16px;
                font-size: 14px;
                font-weight: 500;
                color: #495057;
                background-color: #fff;
                transition: all 0.3s ease;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            }

            .location-select:focus {
                border-color: #556ee6;
                box-shadow: 0 0 0 0.2rem rgba(85, 110, 230, 0.25);
            }

            .location-select:disabled {
                background-color: #f8f9fa;
                color: #6c757d;
                cursor: not-allowed;
                opacity: 0.7;
            }

            .form-text {
                font-size: 12px;
                color: #6c757d;
                margin-top: 4px;
                display: flex;
                align-items: center;
            }

            .form-text i {
                font-size: 14px;
                margin-right: 4px;
                color: #556ee6;
            }

            .form-label {
                font-weight: 600;
                color: #495057;
                margin-bottom: 8px;
            }

            .form-label i {
                color: #556ee6;
                margin-right: 6px;
            }

            @media (max-width: 768px) {
                .location-select {
                    font-size: 16px;
                }
            }

            .wilayah-override-trigger {
                font-size: 13px;
                font-weight: 500;
                text-decoration: none;
            }

            .wilayah-override-trigger:disabled {
                opacity: 0.45;
                pointer-events: none;
            }

            .wilayah-override-panel__body {
                border: 1px dashed #556ee6;
                border-radius: 10px;
                padding: 14px 16px;
                background: #f8f9ff;
                animation: fadeIn 0.25s ease-in-out;
            }

            .wilayah-override-panel__heading {
                font-size: 13px;
                font-weight: 600;
                color: #556ee6;
                margin-bottom: 0;
            }

            .wilayah-override-panel__label {
                font-size: 13px;
                font-weight: 600;
                margin-bottom: 6px;
            }

            .jamaah-review-group {
                border: 1px solid #e9ecef;
                border-radius: 10px;
                padding: 16px;
                background: #fff;
            }

            .jamaah-review-group-title {
                color: #556ee6;
                font-weight: 600;
                margin-bottom: 12px;
                padding-bottom: 8px;
                border-bottom: 1px solid #eef1ff;
            }

            .jamaah-review-label {
                font-size: 12px;
                color: #74788d;
                margin-bottom: 4px;
            }

            .jamaah-review-value {
                font-size: 14px;
                font-weight: 600;
                color: #495057;
                word-break: break-word;
            }
        </style>
    @endpush
@endonce
