<style>
    .trust-intro {
        background: var(--phu-surface, #FFFFFF);
        color: var(--phu-text, #333333);
        border: 1px solid var(--phu-border, #E2E2E2);
        border-radius: 20px;
        padding: min(4vw, 1.75rem) min(5vw, 2rem);
        margin-bottom: 1.5rem;
        box-shadow: 0 6px 24px rgba(226, 167, 18, 0.06);
    }

    .trust-intro h3 {
        font-family: var(--phu-heading-font, "Poppins", sans-serif);
        font-weight: 700;
        margin-bottom: 0.35rem;
        font-size: clamp(18px, 2.5vw, 22px);
        color: var(--phu-text, #333333);
    }

    .trust-intro__lead {
        color: var(--phu-text-muted, #5a5a5a);
        font-size: clamp(14px, 2vw, 16px);
        margin-bottom: 1.1rem;
        line-height: 1.6;
    }

    .trust-intro__steps {
        list-style: none;
        padding: 0;
        margin: 0 0 1.25rem;
        display: grid;
        gap: 0.65rem;
    }

    .trust-intro__steps li {
        display: flex;
        align-items: flex-start;
        gap: 0.65rem;
        font-size: clamp(14px, 2vw, 15px);
        line-height: 1.6;
        color: var(--phu-text, #333333);
    }

    .trust-intro__steps li i {
        color: var(--phu-accent, #e2a712);
        margin-top: 0.2rem;
        flex-shrink: 0;
    }

    .trust-legend {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .trust-legend__item {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.35rem 0.7rem;
        border-radius: 999px;
        font-size: 0.78rem;
        font-weight: 600;
        background: rgba(255, 255, 255, 0.5);
        border: 1px solid var(--phu-border, #CCCCCC);
        color: var(--phu-text, #333333);
    }

    .trust-legend__stars {
        color: var(--phu-gold, #C9A635);
        letter-spacing: -1px;
    }

    .trust-intro__icon {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        background: rgba(226, 167, 18, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        color: var(--phu-accent, #e2a712);
        flex-shrink: 0;
    }

    .trust-card-label {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: var(--phu-text-muted, #5a5a5a);
        margin-bottom: 0.35rem;
    }

    .trust-card-row {
        padding: 0.75rem 1rem;
        background: rgba(255, 255, 255, 0.4);
        border-radius: 12px;
        margin-bottom: 0.75rem;
        border: 1px solid var(--phu-border, #CCCCCC);
    }

    .trust-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.45rem 0.85rem;
        border-radius: 999px;
        font-size: 0.82rem;
        font-weight: 700;
        letter-spacing: 0.01em;
    }

    .trust-badge--compact {
        font-size: 0.72rem;
        padding: 0.35rem 0.65rem;
    }

    .trust-badge__stars {
        display: inline-flex;
        gap: 0.12rem;
        font-size: 0.78rem;
    }

    .trust-badge--compact .trust-badge__stars {
        font-size: 0.65rem;
    }

    .trust-badge--success {
        background: rgba(226, 167, 18, 0.12);
        color: var(--phu-accent-dark, #c8940e);
        border: 1px solid rgba(226, 167, 18, 0.3);
    }

    .trust-badge--info {
        background: rgba(226, 167, 18, 0.1);
        border: 1px solid rgba(226, 167, 18, 0.25);
    }

    .trust-badge--warning {
        background: rgba(201, 166, 53, 0.15);
        color: var(--phu-gold-dark, #a88a2b);
        border: 1px solid rgba(201, 166, 53, 0.35);
    }

    .trust-badge--danger {
        background: rgba(220, 53, 69, 0.1);
        color: #a71d2a;
        border: 1px solid rgba(220, 53, 69, 0.25);
    }

    .trust-badge--muted {
        background: rgba(255, 255, 255, 0.5);
        color: var(--phu-text-muted, #5a5a5a);
        border: 1px solid var(--phu-border, #CCCCCC);
    }

    .trust-card-link {
        text-decoration: none;
        color: inherit;
        display: block;
        height: 100%;
    }

    .trust-card-link:hover {
        color: inherit;
    }

    .travel-card__footer {
        padding: 0 1.8rem 1.5rem;
        margin-top: -0.5rem;
    }

    .btn-trust-profile {
        width: 100%;
        border-radius: 12px;
        font-weight: 700;
        font-size: clamp(14px, 2vw, 15px);
        padding: 0.65rem 1rem;
        border: 2px solid var(--phu-accent, #e2a712);
        color: var(--phu-accent, #e2a712);
        background: var(--phu-surface, #FFFFFF);
        transition: all 0.25s ease;
    }

    .btn-trust-profile:hover {
        background: var(--phu-gold, #C9A635);
        border-color: var(--phu-gold, #C9A635);
        color: var(--phu-text, #333333);
    }

    .trust-hint-box {
        display: none;
        margin-top: 0.75rem;
        padding: 0.85rem 1rem;
        border-radius: 12px;
        background: rgba(226, 167, 18, 0.06);
        border: 1px solid rgba(226, 167, 18, 0.2);
        font-size: clamp(14px, 2vw, 15px);
        line-height: 1.6;
        color: var(--phu-text, #333333);
    }

    .trust-hint-box.is-visible {
        display: block;
    }

    .trust-hint-box__label {
        font-weight: 700;
        color: var(--phu-text, #333333);
    }

    /* Profil publik */
    .trust-hero {
        background: var(--phu-surface, #FFFFFF);
        border: 1px solid var(--phu-border, #E2E2E2);
        border-radius: 24px;
        box-shadow: 0 8px 30px rgba(226, 167, 18, 0.06);
        overflow: hidden;
        margin-bottom: 2rem;
    }

    .trust-hero__top {
        background: linear-gradient(135deg, #2B2B2B 0%, #1a1a1a 100%);
        color: var(--phu-white, #FFFFFF);
        padding: min(4vw, 2rem) min(5vw, 2.5rem);
    }

    .trust-hero__score-wrap {
        text-align: center;
        padding: min(4vw, 2rem) min(3vw, 1.5rem);
    }

    .trust-gauge {
        width: 160px;
        height: 160px;
        border-radius: 50%;
        margin: 0 auto 1rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        border: 8px solid var(--phu-border, #CCCCCC);
        position: relative;
    }

    .trust-gauge__number {
        font-size: clamp(36px, 5vw, 44px);
        font-weight: 800;
        line-height: 1;
        color: var(--phu-accent, #e2a712);
    }

    .trust-gauge__of {
        font-size: 0.8rem;
        color: var(--phu-text-muted, #5a5a5a);
        font-weight: 600;
    }

    .trust-gauge__label {
        display: inline-block;
        padding: 0.45rem 1rem;
        border-radius: 999px;
        font-weight: 700;
        font-size: clamp(14px, 2vw, 15px);
        margin-bottom: 0.75rem;
    }

    .trust-gauge__stars {
        color: var(--phu-gold, #C9A635);
        font-size: 1.1rem;
        margin-bottom: 0.5rem;
    }

    .signal-card {
        border: 1px solid var(--phu-border, #E2E2E2) !important;
        border-radius: 16px;
        background: var(--phu-surface, #FFFFFF) !important;
        box-shadow: 0 4px 18px rgba(226, 167, 18, 0.04);
        height: 100%;
        transition: transform 0.2s ease;
    }

    .signal-card:hover {
        transform: translateY(-3px);
        border-color: var(--phu-accent, #e2a712) !important;
    }

    .signal-card__icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        margin-bottom: 0.75rem;
    }

    .signal-card__icon--success { background: rgba(226, 167, 18, 0.12); color: var(--phu-accent, #e2a712); }
    .signal-card__icon--info { background: rgba(226, 167, 18, 0.08); color: var(--phu-accent, #e2a712); }
    .signal-card__icon--warning { background: rgba(201, 166, 53, 0.15); color: var(--phu-gold, #C9A635); }

    .trust-disclaimer {
        background: rgba(255, 255, 255, 0.4);
        border: 1px solid var(--phu-border, #CCCCCC);
        border-radius: 14px;
        padding: 1.25rem 1.5rem;
        font-size: clamp(13px, 2vw, 14px);
        color: var(--phu-text-muted, #5a5a5a);
        line-height: 1.7;
    }

    .trust-faq .accordion-button {
        font-weight: 600;
        color: var(--phu-text, #333333);
        background: var(--phu-surface, #FFFFFF);
    }

    .trust-faq .accordion-button:not(.collapsed) {
        background: rgba(226, 167, 18, 0.08);
        color: var(--phu-accent, #e2a712);
    }
</style>
