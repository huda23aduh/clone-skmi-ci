 <!--begin::Sidebar-->
 <aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
    <!--begin::Sidebar Brand-->
    <div class="sidebar-brand">
      <!--begin::Brand Link-->
      <a href="./index.html" class="brand-link">
        <!--begin::Brand Image-->
        <img
          src="<?= base_url('adminlte/assets/img/AdminLTELogo.png') ?>"
          alt="AdminLTE Logo"
          class="brand-image opacity-75 shadow"
        />
        <!--end::Brand Image-->
        <!--begin::Brand Text-->
        <!-- <span class="brand-text fw-light">AdminLTE 4</span> -->
        <!--end::Brand Text-->
      </a>
      <!--end::Brand Link-->
    </div>
    <!--end::Sidebar Brand-->
    <!--begin::Sidebar Wrapper-->
    <div class="sidebar-wrapper">
      <nav class="mt-2">
        <!--begin::Sidebar Menu-->
        <ul
          class="nav sidebar-menu flex-column"
          data-lte-toggle="treeview"
          role="navigation"
          aria-label="Main navigation"
          data-accordion="false"
          id="navigation"
        >
          <li class="nav-item">
            <a href="/" class="nav-link">
              <i class="nav-icon bi bi-archive"></i>
              <!-- <i class="fa-solid fa-folder"></i> -->
              <p><?= lang('app.mydrive') ?></p>
            </a>
          </li>

          <li class="nav-item">
            <a href="<?= base_url('/starred') ?>" class="nav-link">
              <i class="nav-icon bi bi-star"></i>
              <p><?= lang('app.priorityfiles') ?></p>
            </a>
          </li>

          <li class="nav-item">
            <a href="/recycle-bin" class="nav-link">
              <i class="nav-icon bi bi-trash3"></i>
              <p><?= lang('app.recyclebin') ?></p>
            </a>
          </li>

          <li class="nav-item">
            <a href="/summary" class="nav-link">
              <i class="nav-icon bi bi-graph-up"></i>
              <p><?= lang('app.summary') ?></p>
            </a>
          </li>


          <!-- <li class="nav-item">
            <a href="<?= base_url('/activity-log') ?>" class="nav-link">
              <i class="nav-icon bi bi-activity"></i>
              <p><?= lang('app.activitylog') ?></p>
            </a>
          </li> -->


          <li class="nav-item">
            <a href="/members" class="nav-link">
              <i class="nav-icon bi bi-people"></i>
              <p><?= lang('app.members') ?></p>
            </a>
          </li>

          <li class="nav-item">
            <a href="<?= base_url('/profile') ?>" class="nav-link">
              <i class="nav-icon bi bi-person-circle"></i>
              <p><?= lang('app.profile') ?></p>
            </a>
          </li>

          <?php $user = session()->get('user'); ?>

          <?php if ((session()->get('user')['isAdmin'] ?? null) === "1"): ?>
              <li class="nav-item">
                <a href="<?= base_url('/control-center') ?>" class="nav-link">
                  <i class="nav-icon bi bi-person-fill-gear"></i>
                  <p><?= lang('app.controlcenter') ?></p>
                </a>
              </li>
          <?php endif; ?>

          <li class="nav-item">
            <a href="<?= base_url('/logout') ?>" class="nav-link">
              <i class="nav-icon bi bi-box-arrow-right"></i>
              <p><?= lang('app.logout') ?></p>
            </a>
          </li>
        </ul>
        <!--end::Sidebar Menu-->
      </nav>
    </div>
    <!--end::Sidebar Wrapper-->

    <!-- Sidebar Footer -->
    <div class="sidebar-footer p-3 text-center text-white small" id="storageFooter" style="
        position:absolute;
        bottom:0;
        width:100%;
        background:rgba(255,255,255,0.08);
        backdrop-filter: blur(4px);
    ">
        Loading...
    </div>

  </aside>
  <!--end::Sidebar-->

<?= $this->include('partials/components/sidebar_footer_script') ?>
