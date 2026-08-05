(function () {
    var config = window.__PHU_REVERB__;
    if (!config || !window.Echo) {
        return;
    }

    var bellBtn = document.getElementById('page-header-notifications-dropdown');
    var listEl = document.getElementById('notification-dropdown-list');
    if (!bellBtn || !listEl) {
        return;
    }

    var markAllForm = document.getElementById('notification-mark-all-form');

    function escapeHtml(text) {
        var div = document.createElement('div');
        div.textContent = text == null ? '' : String(text);
        return div.innerHTML;
    }

    function truncate(text, max) {
        var value = text == null ? '' : String(text);
        return value.length > max ? value.slice(0, max) : value;
    }

    function currentUnreadCount() {
        var badge = bellBtn.querySelector('.notification-unread-badge');
        if (!badge) {
            return 0;
        }

        return parseInt(badge.getAttribute('data-count') || '0', 10) || 0;
    }

    function setUnreadCount(count) {
        var badge = bellBtn.querySelector('.notification-unread-badge');

        if (count <= 0) {
            if (badge) {
                badge.remove();
            }
            if (markAllForm) {
                markAllForm.classList.add('d-none');
            }
            return;
        }

        if (!badge) {
            badge = document.createElement('span');
            badge.className = 'badge bg-danger rounded-pill position-absolute end-0 notification-unread-badge';
            badge.style.fontSize = '10px';
            badge.style.top = '6px';
            bellBtn.appendChild(badge);
        }

        badge.setAttribute('data-count', String(count));
        badge.textContent = count > 9 ? '9+' : String(count);

        if (markAllForm) {
            markAllForm.classList.remove('d-none');
        }
    }

    function prependNotification(data) {
        var empty = listEl.querySelector('.notification-empty-state');
        if (empty) {
            empty.remove();
        }

        var url = data.url || config.indexUrl;
        var item = document.createElement('a');
        item.href = url;
        item.className = 'dropdown-item notify-item py-2 border-bottom notification-live-item';
        item.innerHTML =
            '<div class="d-flex">' +
                '<div class="flex-grow-1">' +
                    '<h6 class="mb-1 fs-13">' + escapeHtml(data.title || 'Notifikasi') + '</h6>' +
                    '<p class="mb-0 text-muted fs-12">' + escapeHtml(truncate(data.message || '', 80)) + '</p>' +
                    '<small class="text-muted">Baru saja</small>' +
                '</div>' +
            '</div>';
        listEl.insertBefore(item, listEl.firstChild);

        var items = listEl.querySelectorAll('.notification-live-item, .notification-static-item');
        if (items.length > 5) {
            items[items.length - 1].remove();
        }
    }

    window.Echo.private('App.Models.User.' + config.userId)
        .notification(function (notification) {
            setUnreadCount(currentUnreadCount() + 1);
            prependNotification(notification);
        });
})();
