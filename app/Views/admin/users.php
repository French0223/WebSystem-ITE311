<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<?php
    /** @var array $users */
    /** @var array $roles */
    $session      = session();
    $errors       = (array) ($session->getFlashdata('errors') ?? []);
    $formContext  = $session->getFlashdata('formContext');
    $oldInput     = (array) ($session->getFlashdata('_ci_old_input') ?? []);

    $isCreateContext = static fn(string $context = null): bool => ($context === 'create');
    $isUpdateContext = static fn(?string $context, int $id): bool => ($context === 'update-' . $id);

    $contextOld = static function (?string $context, ?array $old, string $field, $default = '', ?int $id = null) {
        if ($context === null || empty($old)) {
            return $default;
        }

        if ($context === 'create' && $id === null) {
            return $old[$field] ?? $default;
        }

        if ($id !== null && $context === 'update-' . $id) {
            return $old[$field] ?? $default;
        }

        return $default;
    };
?>
<div class="d-flex align-items-center justify-content-between mb-4">
  <div>
    <h2 class="text-primary mb-0">User Management</h2>
    <p class="text-muted mb-0">Create, update, or remove users and assign their roles.</p>
  </div>
  <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUserModal">
    <i class="fa fa-user-plus me-2"></i>Add User
  </button>
</div>

<div class="card shadow-sm">
  <div class="card-body">
    <?php if (empty($users)): ?>
      <div class="text-center text-muted py-4">No users found.</div>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table align-middle">
          <thead>
            <tr>
              <th>#</th>
              <th>Name</th>
              <th>Email</th>
              <th>Role</th>
              <th class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($users as $index => $user): ?>
              <tr>
                <td><?= $index + 1 ?></td>
                <td><?= esc($user['name']) ?></td>
                <td><?= esc($user['email']) ?></td>
                <td>
                  <span class="badge <?= $user['role'] === 'admin' ? 'bg-danger' : ($user['role'] === 'instructor' ? 'bg-info text-dark' : 'bg-secondary') ?>">
                    <?= ucfirst(esc($user['role'])) ?>
                  </span>
                </td>
                <td class="text-end">
                  <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editUserModal<?= $user['id'] ?>">
                    <i class="fa fa-pen"></i>
                  </button>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</div>

<!-- Create Modal -->
<div class="modal fade" id="createUserModal" tabindex="-1" aria-labelledby="createUserModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="createUserModalLabel">Add New User</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="<?= base_url('admin/users') ?>" method="post">
        <?= csrf_field() ?>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text" name="name" class="form-control <?= isset($errors['name']) && $isCreateContext($formContext ?? '') ? 'is-invalid' : '' ?>" value="<?= esc($contextOld($formContext, $oldInput, 'name')) ?>" required>
            <?php if (isset($errors['name']) && $isCreateContext($formContext ?? '')): ?>
              <div class="invalid-feedback"><?= esc($errors['name']) ?></div>
            <?php endif; ?>
          </div>
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control <?= isset($errors['email']) && $isCreateContext($formContext ?? '') ? 'is-invalid' : '' ?>" value="<?= esc($contextOld($formContext, $oldInput, 'email')) ?>" required>
            <?php if (isset($errors['email']) && $isCreateContext($formContext ?? '')): ?>
              <div class="invalid-feedback"><?= esc($errors['email']) ?></div>
            <?php endif; ?>
          </div>
          <div class="mb-3">
            <label class="form-label">Role</label>
            <select name="role" class="form-select <?= isset($errors['role']) && $isCreateContext($formContext ?? '') ? 'is-invalid' : '' ?>" required>
              <?php foreach ($roles as $roleKey => $roleLabel): ?>
                <option value="<?= esc($roleKey) ?>" <?= $contextOld($formContext, $oldInput, 'role') === $roleKey ? 'selected' : '' ?>><?= esc($roleLabel) ?></option>
              <?php endforeach; ?>
            </select>
            <?php if (isset($errors['role']) && $isCreateContext($formContext ?? '')): ?>
              <div class="invalid-feedback"><?= esc($errors['role']) ?></div>
            <?php endif; ?>
          </div>
          <div class="mb-0">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control <?= isset($errors['password']) && $isCreateContext($formContext ?? '') ? 'is-invalid' : '' ?>" required>
            <?php if (isset($errors['password']) && $isCreateContext($formContext ?? '')): ?>
              <div class="invalid-feedback"><?= esc($errors['password']) ?></div>
            <?php endif; ?>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Create</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php foreach ($users as $user): ?>
  <?php $updateContext = $isUpdateContext($formContext ?? null, (int) $user['id']); ?>
  <!-- Edit Modal -->
  <div class="modal fade" id="editUserModal<?= $user['id'] ?>" tabindex="-1" aria-labelledby="editUserModalLabel<?= $user['id'] ?>" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editUserModalLabel<?= $user['id'] ?>">Edit User</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form action="<?= base_url('admin/users/' . $user['id'] . '/update') ?>" method="post">
          <?= csrf_field() ?>
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">Name</label>
              <input type="text" name="name" class="form-control <?= isset($errors['name']) && $updateContext ? 'is-invalid' : '' ?>" value="<?= esc($contextOld($formContext, $oldInput, 'name', $user['name'], (int) $user['id'])) ?>" required>
              <?php if (isset($errors['name']) && $updateContext): ?>
                <div class="invalid-feedback"><?= esc($errors['name']) ?></div>
              <?php endif; ?>
            </div>
            <div class="mb-3">
              <label class="form-label">Email</label>
              <input type="email" name="email" class="form-control <?= isset($errors['email']) && $updateContext ? 'is-invalid' : '' ?>" value="<?= esc($contextOld($formContext, $oldInput, 'email', $user['email'], (int) $user['id'])) ?>" required>
              <?php if (isset($errors['email']) && $updateContext): ?>
                <div class="invalid-feedback"><?= esc($errors['email']) ?></div>
              <?php endif; ?>
            </div>
            <div class="mb-3">
              <label class="form-label">Role</label>
              <select name="role" class="form-select <?= isset($errors['role']) && $updateContext ? 'is-invalid' : '' ?>" required>
                <?php foreach ($roles as $roleKey => $roleLabel): ?>
                  <?php
                    $selectedRole = $contextOld($formContext, $oldInput, 'role', $user['role'], (int) $user['id']);
                  ?>
                  <option value="<?= esc($roleKey) ?>" <?= $selectedRole === $roleKey ? 'selected' : '' ?>><?= esc($roleLabel) ?></option>
                <?php endforeach; ?>
              </select>
              <?php if (isset($errors['role']) && $updateContext): ?>
                <div class="invalid-feedback"><?= esc($errors['role']) ?></div>
              <?php endif; ?>
            </div>
            <div class="mb-0">
              <label class="form-label">Password (leave blank to keep current)</label>
              <input type="password" name="password" class="form-control <?= isset($errors['password']) && $updateContext ? 'is-invalid' : '' ?>">
              <?php if (isset($errors['password']) && $updateContext): ?>
                <div class="invalid-feedback"><?= esc($errors['password']) ?></div>
              <?php endif; ?>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Save Changes</button>
          </div>
        </form>
      </div>
    </div>
  </div>

<?php endforeach; ?>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
  <script>
    window.addEventListener('DOMContentLoaded', function () {
      var formContext = <?= json_encode($formContext ?? '') ?>;
      if (!formContext) return;

      if (formContext === 'create') {
        var createModal = document.getElementById('createUserModal');
        if (createModal) {
          var modal = new bootstrap.Modal(createModal);
          modal.show();
        }
        return;
      }

      if (formContext.startsWith('update-')) {
        var id = formContext.split('-')[1];
        var editModal = document.getElementById('editUserModal' + id);
        if (editModal) {
          var modal = new bootstrap.Modal(editModal);
          modal.show();
        }
      }
    });
  </script>
<?= $this->endSection() ?>
