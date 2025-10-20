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
                  <p>My Drive</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="/recycle-bin" class="nav-link">
                  <i class="nav-icon bi bi-star"></i>
                  <p>Priority File</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="/recycle-bin" class="nav-link">
                  <i class="nav-icon bi bi-trash3"></i>
                  <p>Recycle Bin</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="./generate/theme.html" class="nav-link">
                  <i class="nav-icon bi bi-graph-up"></i>
                  <p>Summary</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="./generate/theme.html" class="nav-link">
                  <i class="nav-icon bi bi-people"></i>
                  <p>Members</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="./generate/theme.html" class="nav-link">
                  <i class="nav-icon bi bi-person-circle"></i>
                  <p>Profile</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="./generate/theme.html" class="nav-link">
                  <i class="nav-icon bi bi-box-arrow-right"></i>
                  <p>Logout</p>
                </a>
              </li>
            </ul>
            <!--end::Sidebar Menu-->
          </nav>
        </div>
        <!--end::Sidebar Wrapper-->
      </aside>
      <!--end::Sidebar-->