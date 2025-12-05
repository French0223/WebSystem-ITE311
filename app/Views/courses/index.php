<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="mb-4">
  <h1 class="h3 text-primary mb-1">Browse Courses</h1>
  <p class="text-muted mb-0">Search the catalog or filter instantly to find the right class.</p>
</div>

<div class="card border-0 shadow-sm mb-4">
  <div class="card-body">
    <form id="searchForm" class="row g-3 align-items-center">
      <div class="col-md-9">
        <div class="input-group">
          <span class="input-group-text bg-white"><i class="fa-solid fa-magnifying-glass text-primary"></i></span>
          <input type="text" class="form-control" id="searchInput" name="search_term" placeholder="Search title, category, or description" value="<?= esc($searchTerm ?? '') ?>" autocomplete="off">
        </div>
      </div>
      <div class="col-md-3 d-grid d-md-flex">
        <button class="btn btn-primary w-100" type="submit">
          <i class="fa-solid fa-search me-1"></i>search
        </button>
      </div>
    </form>
    <div id="searchFeedback" class="text-muted small mt-2"></div>
  </div>
</div>

<div id="coursesContainer" class="row g-3">
  <?php if (empty($courses)): ?>
    <div class="col-12">
      <div class="alert alert-info">No courses available. Seed the database to continue.</div>
    </div>
  <?php else: ?>
    <?php foreach ($courses as $course): ?>
      <?php
        $title       = $course['title'] ?? 'Untitled Course';
        $description = $course['description'] ?? 'No description provided yet.';
        $category    = $course['category'] ?? 'General';
        $level       = $course['level'] ?? 'All levels';
        $duration    = $course['duration'] ?? null;
        $searchBlob  = strtolower($title . ' ' . $description . ' ' . $category . ' ' . $level);
      ?>
      <div class="col-md-4 course-column" data-search="<?= esc($searchBlob) ?>">
        <div class="card course-card h-100 border-0 shadow-sm">
          <div class="card-body d-flex flex-column">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <span class="badge bg-light text-dark text-uppercase small"><?= esc($category) ?></span>
              <span class="text-muted small"><?= esc(ucwords($level)) ?></span>
            </div>
            <h5 class="card-title mb-2"><?= esc($title) ?></h5>
            <p class="card-text text-muted flex-grow-1" style="min-height:72px;">
              <?= esc($description) ?>
            </p>
            <div class="d-flex justify-content-between align-items-center mt-3">
              <span class="small text-muted">
                <i class="fa-regular fa-clock me-1"></i>
                <?= $duration ? esc($duration) . ' hrs' : 'Flexible length' ?>
              </span>
              <button class="btn btn-outline-primary btn-sm" type="button" disabled>View details</button>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script>
(function ($) {
  const $form = $('#searchForm');
  const $input = $('#searchInput');
  const $feedback = $('#searchFeedback');
  const $container = $('#coursesContainer');

  const escapeHtml = (value) => $('<div>').text(value ?? '').html();

  function renderCourses(courses) {
    if (!Array.isArray(courses) || courses.length === 0) {
      $container.html('<div class="col-12"><div class="alert alert-info">No courses found matching your search.</div></div>');
      return;
    }

    const cards = courses.map((course) => {
      const title = course.title || 'Untitled Course';
      const description = course.description || 'No description provided yet.';
      const category = course.category || 'General';
      const level = course.level ? course.level.charAt(0).toUpperCase() + course.level.slice(1) : 'All levels';
      const duration = course.duration ? `${course.duration} hrs` : 'Flexible length';
      const searchBlob = (title + ' ' + description + ' ' + category + ' ' + level).toLowerCase();

      return `
        <div class="col-md-4 course-column" data-search="${escapeHtml(searchBlob)}">
          <div class="card course-card h-100 border-0 shadow-sm">
            <div class="card-body d-flex flex-column">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="badge bg-light text-dark text-uppercase small">${escapeHtml(category)}</span>
                <span class="text-muted small">${escapeHtml(level)}</span>
              </div>
              <h5 class="card-title mb-2">${escapeHtml(title)}</h5>
              <p class="card-text text-muted flex-grow-1" style="min-height:72px;">${escapeHtml(description)}</p>
              <div class="d-flex justify-content-between align-items-center mt-3">
                <span class="small text-muted"><i class="fa-regular fa-clock me-1"></i> ${escapeHtml(duration)}</span>
                <button class="btn btn-outline-primary btn-sm" type="button" disabled>View details</button>
              </div>
            </div>
          </div>
        </div>`;
    }).join('');

    $container.html(cards);
  }

  function applyClientFilter(term) {
    const lowered = term.toLowerCase();
    let visible = 0;

    $('.course-column').each(function () {
      const haystack = ($(this).data('search') || '').toString();
      const match = haystack.indexOf(lowered) !== -1;
      $(this).toggle(match);
      visible += match ? 1 : 0;
    });

    if (!visible) {
      $feedback.text('No visible courses for the current filter. Try another keyword or run a server search.');
    } else if (lowered.length) {
      $feedback.text(`Showing ${visible} course${visible === 1 ? '' : 's'} that match your filter.`);
    } else {
      $feedback.text('Tip: type to filter instantly or run a server search for database results.');
    }
  }

  $input.on('keyup', function () {
    applyClientFilter($(this).val());
  });

  $form.on('submit', function (e) {
    e.preventDefault();
    const term = $input.val().trim();

    $feedback.text('Searching the database...');

    $.getJSON('<?= site_url('courses/search') ?>', { search_term: term })
      .done(function (response) {
        renderCourses(response.courses || []);
        applyClientFilter(term);
        const count = response.count ?? (response.courses ? response.courses.length : 0);
        $feedback.text(count ? `Found ${count} course${count === 1 ? '' : 's'} for "${term || 'all'}".` : 'No courses found matching your search.');
      })
      .fail(function () {
        $feedback.text('Unable to complete the search right now. Please try again later.');
      });
  });

  if ($input.val()) {
    applyClientFilter($input.val());
  }
})(jQuery);
</script>
<?= $this->endSection() ?>