<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= esc($title ?? 'Learning Management System') ?></title>
    <!-- Bootstrap + Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    <style>
      body { background-color: #f8f9fa; }
      .top-header { background: #fff; padding: 12px 20px; box-shadow: 0 2px 4px rgba(0,0,0,.06); }
      .nav .nav-link { color: #0d6efd; }
      .nav .nav-link:hover { color: #0b5ed7; }
      .dropdown-toggle { background: #0d6efd; color: #fff; border: none; padding: 6px 12px; border-radius: 6px; }
      .dropdown-menu-custom { display:none; right:0; }
      .dropdown-menu-custom.show { display:block; }
      /* Notification dropdown card UI */
      #notifMenu { width: 360px; }
      .notif-card { background: #fff; border: 1px solid #e9ecef; border-radius: .75rem; box-shadow: 0 2px 8px rgba(0,0,0,.06); }
      .notif-card-header { padding: .5rem .75rem; font-weight: 600; color: #495057; border-bottom: 1px solid #e9ecef; display:flex; align-items:center; justify-content:space-between; }
      .notif-card-body { max-height: 360px; overflow: auto; padding: .75rem; }
      .notif-item { background: #fff; border-left: 4px solid #0d6efd; padding: .75rem; border-radius: .25rem; border: 1px solid #eef1f4; }
      .notif-item + .notif-item { margin-top: .5rem; }
      .notif-item.read { border-left-color: #bfc8d0; opacity: .9; }
      .notif-item:hover { box-shadow: 0 2px 6px rgba(13,110,253,.08); }
      .notif-item .title { font-weight: 600; color: #212529; }
      .notif-item .time { font-size: .8rem; color: #6c757d; }
      .notif-actions .btn { padding: .25rem .5rem; }
      .notif-card-body::-webkit-scrollbar { width: 8px; }
      .notif-card-body::-webkit-scrollbar-thumb { background-color: rgba(0,0,0,.15); border-radius: 4px; }
      .notif-card-body::-webkit-scrollbar-track { background: transparent; }
      .title { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    </style>
</head>
<body class="bg-light">

    <?= view('templates/header', ['title' => $title ?? 'LMS']) ?>

    <?php
      $session = session();
      $success = $session->getFlashdata('success');
      $error   = $session->getFlashdata('error');
      $warning = $session->getFlashdata('warning');
      $info    = $session->getFlashdata('info');
    ?>
    <?php if ($success || $error || $warning || $info): ?>
      <div class="container mt-3">
        <?php if ($success): ?>
          <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert" aria-live="polite">
            <i class="fa-solid fa-circle-check me-2"></i>
            <div><?= esc($success) ?></div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        <?php endif; ?>
        <?php if ($error): ?>
          <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert" aria-live="assertive">
            <i class="fa-solid fa-triangle-exclamation me-2"></i>
            <div><?= esc($error) ?></div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        <?php endif; ?>
        <?php if ($warning): ?>
          <div class="alert alert-warning alert-dismissible fade show d-flex align-items-center" role="alert" aria-live="polite">
            <i class="fa-solid fa-circle-exclamation me-2"></i>
            <div><?= esc($warning) ?></div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        <?php endif; ?>
        <?php if ($info): ?>
          <div class="alert alert-info alert-dismissible fade show d-flex align-items-center" role="alert" aria-live="polite">
            <i class="fa-solid fa-circle-info me-2"></i>
            <div><?= esc($info) ?></div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        <?php endif; ?>
      </div>
    <?php endif; ?>

    <?php $full = trim($this->renderSection('full_content')); ?>
    <?php if ($full !== ''): ?>
      <?= $full ?>
    <?php else: ?>
      <main class="container py-4">
          <?= $this->renderSection('content') ?>
      </main>
    <?php endif; ?>

    <script>
      (function () {
        const isLoggedIn = <?= !empty(session('role')) ? 'true' : 'false' ?>;
        // CSRF support (CI4)
        const CSRF_NAME = '<?= function_exists('csrf_token') ? csrf_token() : 'csrf_token' ?>';
        const CSRF_HASH = '<?= function_exists('csrf_hash') ? csrf_hash() : '' ?>';

        // User menu
        function toggleDropdown() {
          const el = document.getElementById('userDropdown');
          if (el) el.classList.toggle('show');
        }

        // Notifications menu (position near bell and toggle)
        function toggleNotifMenu() {
          const menu = document.getElementById('notifMenu');
          const userMenu = document.getElementById('userDropdown');
          const bell = document.getElementById('notifToggle');
          if (userMenu) userMenu.classList.remove('show');
          if (!menu || !bell) return;
          // Position the menu near the bell using viewport coordinates
          try {
            const rect = bell.getBoundingClientRect();
            const width = 360; // should match #notifMenu width
            menu.style.position = 'fixed';
            menu.style.zIndex = '99999';
            // Compute and clamp within viewport
            const desiredTop = rect.bottom + 8;
            const desiredLeft = rect.right - width;
            const clampedTop = Math.max(8, Math.min(window.innerHeight - 8, desiredTop));
            const clampedLeft = Math.max(8, Math.min(window.innerWidth - (width + 8), desiredLeft));
            menu.style.top = clampedTop + 'px';
            menu.style.left = clampedLeft + 'px';
            // Ensure visible styling
            menu.style.minWidth = width + 'px';
            menu.style.background = '#fff';
            menu.style.border = '1px solid rgba(0,0,0,.15)';
          } catch (e) {}
          const open = menu.style.display === 'block';
          const willOpen = !open;
          menu.style.display = willOpen ? 'block' : 'none';
          if (willOpen) {
            // Force visible rendering in case any global styles interfere
            menu.style.visibility = 'visible';
            menu.style.opacity = '1';
            menu.style.transform = 'none';
            menu.style.pointerEvents = 'auto';
          }
          if (willOpen) {
            try { loadNotifications(); } catch (e) {}
          }
        }

        function renderNotifications(data) {
          const badge = document.getElementById('notifBadge');
          const list = document.getElementById('notifList');
          if (!badge || !list) return;

          const count = (data && data.unread) ? data.unread : 0;
          if (count > 0) {
            badge.textContent = count;
            badge.style.display = 'inline-block';
          } else {
            badge.style.display = 'none';
          }

          list.innerHTML = '';
          const items = (data && data.items) ? data.items : [];
          // Build card wrapper first
          var html = '<div class="notif-card">' +
                     '  <div class="notif-card-header">' +
                     '    <span>Notifications</span>' +
                     '    <span class="badge bg-primary ms-2">' + count + '</span>' +
                     '  </div>' +
                     '  <div class="notif-card-body">';

          if (items.length === 0) {
            html += '<div class="text-center text-muted small py-4">' +
                    '  <i class="fa-regular fa-bell-slash d-block mb-2" style="font-size:1.25rem;"></i>' +
                    '  No new notifications.' +
                    '</div>';
          } else {
            items.forEach(function (n) {
              var isRead = false;
              if (n) {
                // Support multiple conventions and types (int, string, bool)
                var flag = (n.hasOwnProperty('is_read') ? n.is_read : (n.hasOwnProperty('read') ? n.read : undefined));
                if (flag !== undefined) {
                  isRead = (flag == 1 || flag === true || String(flag).toLowerCase() === 'true');
                }
                if (!isRead && n.status) {
                  isRead = (String(n.status).toLowerCase() === 'read');
                }
              }
              html += '\
                <div class="notif-item d-flex align-items-center justify-content-between border rounded-3 p-2' + (isRead ? ' read bg-light opacity-75' : '') + '">\
                  <div class="d-flex align-items-start me-3 flex-grow-1">\
                    <i class="fa-solid fa-bell text-primary me-2 mt-1"></i>\
                    <div>\
                      <div class="title">' + (n && n.message ? n.message : '') + '</div>\
                      <div class="time small text-muted">' + (n && n.created_at ? n.created_at : '') + '</div>\
                    </div>\
                  </div>\
                  ' + (!isRead ? ('<div class="notif-actions d-flex align-items-center"><button class="btn btn-link btn-sm p-0 text-primary text-decoration-none lh-1 d-inline-flex align-items-center justify-content-center" data-id="' + (n && n.id ? n.id : '') + '" title="Mark as read" aria-label="Mark as read"><i class="fa-solid fa-check"></i></button></div>') : '') + '\
                </div>';
            });
          }

          html += '  </div></div>';
          list.innerHTML = html;

          list.querySelectorAll('button[data-id]').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
              e.stopPropagation();
              const id = this.getAttribute('data-id');
              if (!id) return;
              // Optimistic UI: mark as read immediately
              const btnEl = this;
              const itemEl = btnEl.closest('.notif-item');
              const actionsEl = btnEl.parentElement;
              const badge = document.getElementById('notifBadge');
              btnEl.disabled = true;
              if (itemEl) itemEl.classList.add('read');
              if (actionsEl) actionsEl.remove();
              if (badge && badge.style.display !== 'none') {
                var n = parseInt(badge.textContent || '0', 10); if (!isNaN(n) && n > 0) { n = n - 1; }
                if (n > 0) { badge.textContent = n; } else { badge.style.display = 'none'; }
              }
              fetch('<?= site_url('notifications/mark_read') ?>/' + id, {
                method: 'POST',
                headers: {
                  'X-Requested-With': 'XMLHttpRequest',
                  'Content-Type': 'application/x-www-form-urlencoded'
                },
                credentials: 'same-origin',
                body: CSRF_HASH ? new URLSearchParams({ [CSRF_NAME]: CSRF_HASH }) : undefined
              })
                .then(function (res) { if (!res.ok) throw new Error(res.status); return res.json(); })
                .then(function (ok) { if (ok && ok.success) return; /* already updated UI */ else throw new Error('failed'); })
                .catch(function (err) {
                  console.error('Mark as read failed:', err);
                  // Revert UI on failure
                  if (itemEl) itemEl.classList.remove('read');
                  // Recreate actions button if removed
                  if (actionsEl && itemEl && !itemEl.querySelector('.notif-actions')) {
                    const newActions = document.createElement('div'); newActions.className = 'notif-actions d-flex align-items-center';
                    const newBtn = document.createElement('button'); newBtn.className = 'btn btn-link btn-sm p-0 text-primary text-decoration-none lh-1 d-inline-flex align-items-center justify-content-center'; newBtn.setAttribute('data-id', id); newBtn.setAttribute('title', 'Mark as read'); newBtn.setAttribute('aria-label', 'Mark as read'); newBtn.innerHTML = '<i class="fa-solid fa-check"></i>';
                    newActions.appendChild(newBtn);
                    itemEl.appendChild(newActions);
                  }
                  if (btnEl) { btnEl.disabled = false; btnEl.className = 'btn btn-link btn-sm p-0 text-primary text-decoration-none lh-1 d-inline-flex align-items-center justify-content-center'; btnEl.innerHTML = '<i class="fa-solid fa-check"></i>'; }
                  if (badge) {
                    badge.style.display = 'inline-block';
                    var n = parseInt(badge.textContent || '0', 10); if (!isNaN(n)) { badge.textContent = (n + 1); }
                  }
                });
            });
          });

          // No "mark all" action
        }

        function loadNotifications() {
          fetch('<?= site_url('notifications') ?>', { headers: { 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' })
            .then(function (res) { return res.json(); })
            .then(function (data) { renderNotifications(data); })
            .catch(function (err) { console.error('Notifications fetch failed:', err); });
        }

        // DOM Ready bindings
        document.addEventListener('DOMContentLoaded', function () {
          const userBtn = document.querySelector('.user-dropdown .dropdown-toggle');
          if (userBtn) {
            userBtn.addEventListener('click', function (e) {
              e.preventDefault();
              e.stopPropagation();
              toggleDropdown();
            });
          }

          const bell = document.getElementById('notifToggle');
          if (bell) {
            bell.addEventListener('click', function (e) {
              e.preventDefault();
              e.stopPropagation();
              toggleNotifMenu();
            });
          }

          // Close menus on outside click
          window.addEventListener('click', function (event) {
            const userMenu = document.getElementById('userDropdown');
            const userButton = document.querySelector('.user-dropdown .dropdown-toggle');
            const notifMenu = document.getElementById('notifMenu');
            const notifButton = document.getElementById('notifToggle');

            if (userMenu && userButton && !userButton.contains(event.target) && !userMenu.contains(event.target)) {
              userMenu.classList.remove('show');
            }
            if (notifMenu && notifButton && !notifButton.contains(event.target) && !notifMenu.contains(event.target)) {
              notifMenu.style.display = 'none';
            }
          });

          // Notifications init/polling only when logged in
          if (isLoggedIn) {
            loadNotifications();
            setInterval(function () {
              if (document.visibilityState === 'visible') loadNotifications();
            }, 60000);
          }
        });
      })();
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
      // Auto-dismiss flash alerts after 4 seconds
      window.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
          document.querySelectorAll('.alert').forEach(function(el){
            try {
              var inst = bootstrap.Alert.getOrCreateInstance(el);
              inst.close();
            } catch(e) {}
          });
        }, 4000);
      });
    </script>
    <?= $this->renderSection('scripts') ?>
</body>
</html>
