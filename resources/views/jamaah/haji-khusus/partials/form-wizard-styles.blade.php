@once
    @push('styles')
        <style>
            #jamaah-haji-khusus-wizard.wizard > .steps {
                display: none !important;
            }

            #jamaah-haji-khusus-wizard .row.g-3 > [class*="col-"] > .mb-3 {
                margin-bottom: 0 !important;
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
            }

            .jamaah-wizard-steps::before {
                content: '';
                position: absolute;
                top: 16px;
                left: 7%;
                right: 7%;
                height: 2px;
                background: var(--bs-border-color);
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
                background: var(--bs-body-bg);
                border: 2px solid var(--bs-border-color);
                color: var(--bs-secondary-color);
            }

            .jamaah-wizard-step-check {
                display: none;
                font-size: 16px;
            }

            .jamaah-wizard-step-label {
                margin-top: 8px;
                font-size: 11px;
                line-height: 1.2;
                color: var(--bs-secondary-color);
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                padding: 0 2px;
            }

            .jamaah-wizard-step.is-active .jamaah-wizard-step-marker {
                border-color: var(--bs-primary);
                background: var(--bs-primary);
                color: #fff;
            }

            .jamaah-wizard-step.is-active .jamaah-wizard-step-label {
                color: var(--bs-primary);
                font-weight: 600;
            }

            .jamaah-wizard-step.is-done .jamaah-wizard-step-marker {
                border-color: var(--bs-success);
                background: var(--bs-success);
                color: #fff;
            }

            .jamaah-wizard-step.is-done .jamaah-wizard-step-number {
                display: none;
            }

            .jamaah-wizard-step.is-done .jamaah-wizard-step-check {
                display: inline-block;
            }

            .jamaah-wizard-step.is-done .jamaah-wizard-step-label {
                color: var(--bs-success);
            }

            .custom-wizard-error,
            .validation-error-message {
                display: block !important;
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
        </style>
    @endpush
@endonce
