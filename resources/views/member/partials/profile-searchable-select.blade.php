<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
<style>
    #member-profile-form .grid > div {
        min-width: 0;
    }
    #member-profile-form .mp-select-field {
        position: relative;
        width: 100%;
        min-height: 48px;
    }
    #member-profile-form .mp-select-field > select.mp-searchable-select {
        position: absolute;
        width: 1px;
        height: 1px;
        padding: 0;
        margin: -1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
        white-space: nowrap;
        border: 0;
        opacity: 0;
        pointer-events: none;
    }
    #member-profile-form .mp-select-field .choices {
        width: 100%;
        margin-bottom: 0;
        position: relative;
        z-index: 1;
    }
    #member-profile-form .mp-select-field .choices.is-open {
        z-index: 40;
    }
    #member-profile-form .choices__inner {
        border-radius: 1rem;
        border: 1px solid rgba(53, 28, 66, 0.12);
        background: rgba(255, 255, 255, 0.95);
        min-height: 48px;
        padding: 0.55rem 2.5rem 0.55rem 1rem;
        font-size: 0.9375rem;
        color: #351c42;
        line-height: 1.35;
        transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease, border-radius 0.15s ease;
    }
    #member-profile-form .choices.is-disabled .choices__inner {
        background: #f1f5f9;
        color: rgba(53, 28, 66, 0.65);
        cursor: not-allowed;
        opacity: 1;
    }
    #member-profile-form .choices.is-focused .choices__inner,
    #member-profile-form .choices.is-open .choices__inner {
        border-color: rgba(150, 89, 149, 0.55);
        background: #fff;
        box-shadow: 0 0 0 4px rgba(150, 89, 149, 0.14);
    }
    #member-profile-form .choices.is-disabled.is-focused .choices__inner,
    #member-profile-form .choices.is-disabled.is-open .choices__inner {
        border-color: rgba(53, 28, 66, 0.12);
        box-shadow: none;
        background: #f1f5f9;
    }
    #member-profile-form .choices.is-open:not(.is-flipped) .choices__inner {
        border-bottom-color: transparent;
        border-radius: 1rem 1rem 0 0;
    }
    #member-profile-form .choices.is-open.is-flipped .choices__inner {
        border-top-color: transparent;
        border-radius: 0 0 1rem 1rem;
    }
    #member-profile-form .choices[data-type*="select-one"]::after {
        right: 1rem;
        margin-top: -2.5px;
        border-width: 5px;
        border-color: rgba(53, 28, 66, 0.55) transparent transparent;
        transition: transform 0.2s ease;
    }
    #member-profile-form .choices.is-open[data-type*="select-one"]::after {
        margin-top: -7.5px;
        border-color: transparent transparent rgba(53, 28, 66, 0.55);
    }
    #member-profile-form .choices.is-disabled[data-type*="select-one"]::after {
        opacity: 0.35;
    }
    #member-profile-form .choices__placeholder {
        color: rgba(53, 28, 66, 0.42);
        opacity: 1;
    }
    #member-profile-form .choices__list--single {
        padding: 0;
    }
    #member-profile-form .choices__list--single .choices__item {
        color: #351c42;
        opacity: 1;
    }
    #member-profile-form .choices.is-disabled .choices__item {
        color: rgba(53, 28, 66, 0.65);
        opacity: 1;
    }
    #member-profile-form .choices[data-type*="select-one"] .choices__inner .choices__input {
        display: block;
        width: 100%;
        background-color: transparent;
        margin: 0;
        padding: 0;
        border: 0;
        font-size: 0.9375rem;
        color: #351c42;
    }
    #member-profile-form .choices__list--dropdown {
        width: 100%;
        margin-top: 0;
        border: 1px solid rgba(150, 89, 149, 0.55);
        border-radius: 0 0 1rem 1rem;
        background: #fff;
        box-shadow: 0 14px 32px rgba(53, 28, 66, 0.14);
        overflow: hidden;
    }
    #member-profile-form .choices.is-flipped .choices__list--dropdown {
        border-radius: 1rem 1rem 0 0;
        border-bottom: 0;
        margin-bottom: 0;
        box-shadow: 0 -10px 28px rgba(53, 28, 66, 0.12);
    }
    #member-profile-form .choices__list--dropdown .choices__list {
        max-height: 240px;
        padding: 0.35rem 0;
    }
    #member-profile-form .choices__list--dropdown .choices__list::-webkit-scrollbar {
        width: 6px;
    }
    #member-profile-form .choices__list--dropdown .choices__list::-webkit-scrollbar-thumb {
        background: rgba(53, 28, 66, 0.22);
        border-radius: 999px;
    }
    #member-profile-form .choices__list--dropdown .choices__item {
        padding: 0.62rem 1rem;
        font-size: 0.9rem;
        color: #351c42;
        line-height: 1.35;
    }
    #member-profile-form .choices__list--dropdown .choices__item--selectable.is-highlighted {
        background-color: rgba(150, 89, 149, 0.12);
    }
    #member-profile-form .choices__list--dropdown .choices__item--selectable.is-selected {
        background-color: rgba(150, 89, 149, 0.08);
        font-weight: 600;
    }
    #member-profile-form .choices__list--dropdown .choices__item--selectable.is-selected.is-highlighted {
        background-color: rgba(150, 89, 149, 0.16);
    }
    #member-profile-form .mp-select-search {
        padding: 0.65rem 0.75rem 0.55rem;
        background: linear-gradient(180deg, #faf8fc 0%, #fff 100%);
        border-bottom: 1px solid rgba(53, 28, 66, 0.06);
    }
    #member-profile-form .mp-select-search .choices__input,
    #member-profile-form .mp-select-search .choices__input--cloned,
    #member-profile-form .choices__list--dropdown > .choices__input,
    #member-profile-form .choices__list--dropdown > .choices__input--cloned,
    #member-profile-form .choices[data-type*="select-one"] .choices__list--dropdown .choices__input {
        display: block;
        width: 100%;
        margin: 0;
        padding: 0.62rem 0.9rem 0.62rem 2.4rem;
        min-height: 42px;
        border: 1px solid rgba(53, 28, 66, 0.12);
        border-radius: 0.75rem;
        background-color: #fff;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%23965995' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: 0.85rem center;
        background-size: 1rem 1rem;
        font-size: 0.875rem;
        font-family: inherit;
        color: #351c42;
        line-height: 1.35;
        box-sizing: border-box;
        box-shadow: inset 0 1px 2px rgba(53, 28, 66, 0.04);
        appearance: none;
        -webkit-appearance: none;
    }
    #member-profile-form .mp-select-search .choices__input::placeholder,
    #member-profile-form .mp-select-search .choices__input--cloned::placeholder,
    #member-profile-form .choices__list--dropdown > .choices__input::placeholder,
    #member-profile-form .choices__list--dropdown > .choices__input--cloned::placeholder {
        color: rgba(53, 28, 66, 0.42);
        opacity: 1;
    }
    #member-profile-form .mp-select-search .choices__input:focus,
    #member-profile-form .mp-select-search .choices__input--cloned:focus,
    #member-profile-form .choices__list--dropdown > .choices__input:focus,
    #member-profile-form .choices__list--dropdown > .choices__input--cloned:focus {
        outline: none;
        border-color: rgba(150, 89, 149, 0.55);
        box-shadow: 0 0 0 3px rgba(150, 89, 149, 0.14);
        background-color: #fff;
    }
    #member-profile-form .choices__list--dropdown .choices__input::-webkit-search-cancel-button,
    #member-profile-form .choices__list--dropdown .choices__input::-webkit-search-decoration,
    #member-profile-form .choices__list--dropdown .choices__input::-webkit-search-results-button,
    #member-profile-form .choices__list--dropdown .choices__input::-webkit-search-results-decoration {
        display: none;
    }
    #member-profile-form .choices__list--dropdown .choices__item--choice.has-no-results,
    #member-profile-form .choices__list--dropdown .choices__item--choice.has-no-choices {
        font-size: 0.8125rem;
        color: rgba(53, 28, 66, 0.55);
        cursor: default;
    }
    #member-profile-form .choices.mp-is-invalid .choices__inner {
        border-color: rgba(220, 38, 38, 0.55);
        box-shadow: 0 0 0 4px rgba(220, 38, 38, 0.12);
    }
</style>
