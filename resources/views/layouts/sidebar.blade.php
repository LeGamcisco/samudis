<ul class="metismenu" id="menu">
    <li>
      <a href="{{ route("dashboard") }}">
        <div class="parent-icon">
          <ion-icon name="bar-chart-outline" class="text-white"></ion-icon>
        </div>
        <div class="menu-title text-white">Dashboard</div>
      </a>
    </li>
    <li>
      <a href="#" class="has-arrow text-white">
        <div class="parent-icon">
          <ion-icon name="stats-chart-outline" class="text-white"></ion-icon>
        </div>
        <div class="menu-title text-white">Measurement</div>
      </a>
      <ul>
        <li>
          <a href="{{ route("measurement.index") }}" class="text-white">
              <ion-icon name="ellipse-outline" class="text-white"></ion-icon> Data
          </a>
        </li>
        <li>
          <a href="{{ route("measurement.analytic.index") }}" class="text-white">
            <ion-icon name="ellipse-outline" class="text-white"></ion-icon> Analytics
          </a>
        </li>
      </ul>
    </li>
    <li>
      <a href="javscript:void(0)" class="has-arrow text-white">
        <div class="parent-icon">
          <ion-icon name="analytics-outline" class="text-white"></ion-icon>
        </div>
        <div class="menu-title text-white">DIS Logs</div>
      </a>
      <ul>
        <li>
          <a href="{{ route('dis-logs.index') }}" class="text-white">
              <ion-icon name="ellipse-outline" class="text-white"></ion-icon> Data
          </a>
        </li>
        <li>
          <a href="{{ route("dis-logs.analytic") }}" class="text-white">
            <ion-icon name="ellipse-outline" class="text-white"></ion-icon> Analytics
          </a>
        </li>
      </ul>
    </li>

   <li class="menu-label text-white text-bold">Settings</li>
      <li>
        <a href="{{ route("settings.parameter.index") }}">
          <div class="parent-icon">
            <ion-icon name="logo-electron" class="text-white"></ion-icon>
          </div>
          <div class="menu-title text-white">Parameters Status</div>
        </a>
      </li>
      {{-- <li>
        <a href="{{ route("settings.schedule-status.index") }}">
          <div class="parent-icon">
            <ion-icon name="stopwatch-outline" class="text-white"></ion-icon>
          </div>
          <div class="menu-title">Schedule Update Status</div>
        </a>
      </li> --}}
      @if (auth()->user()->group_id < 2) {{-- Only show on admin or superuser --}}
      <li>
        <a href="{{ route("settings.alarm.index") }}">
          <div class="parent-icon">
            <ion-icon name="alarm-outline" class="text-white"></ion-icon>
          </div>
          <div class="menu-title text-white">Alarm Notification</div>
        </a>
      </li>
      <li>
        <a href="{{ route("settings.configuration.index") }}">
          <div class="parent-icon">
            <ion-icon name="options-outline" class="text-white"></ion-icon>
          </div>
          <div class="menu-title text-white">Configuration</div>
        </a>
      </li>

      <li class="menu-label text-white text-bold">Backup & Restore</li>
      <li>
        <a href="{{ route("database.backup.index") }}">
          <div class="parent-icon">
            <ion-icon name="server-outline" class="text-white"></ion-icon>
          </div>
          <div class="menu-title text-white">Backup Database</div>
        </a>
      </li>


      <li class="menu-label text-white text-bold">Master Data</li>
      <li>
        <a href="{{ route("master.user.index") }}">
          <div class="parent-icon">
            <ion-icon name="people-outline" class="text-white"></ion-icon>
          </div>
          <div class="menu-title text-white">Users Management</div>
        </a>
      </li>
      <li>
        <a href="{{ route("master.stack.index") }}">
          <div class="parent-icon">
            <ion-icon name="aperture-outline" class="text-white"></ion-icon>
          </div>
          <div class="menu-title text-white">Stack</div>
        </a>
      </li>
      <li>
        <a href="{{ route("master.parameter.index") }}">
          <div class="parent-icon">
            <ion-icon name="flask-outline" class="text-white"></ion-icon>
          </div>
          <div class="menu-title text-white">Parameters</div>
        </a>
      </li>
      {{-- <li>
        <a href="{{ route("master.reference.index") }}">
          <div class="parent-icon">
            <ion-icon name="flask-outline" class="text-white"></ion-icon>
          </div>
          <div class="menu-title">Reference</div>
        </a>
      </li> --}}
   @endif
  </ul>
