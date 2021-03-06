<div class="sidebar" data-color="orange" data-background-color="white" data-image="{{ asset('material') }}/img/sidebar-1.jpg">
  <!--
      Tip 1: You can change the color of the sidebar using: data-color="purple | azure | green | orange | danger"

      Tip 2: you can also add an image using data-image tag
  -->
  <div class="logo">
    <a href="https://creative-tim.com/" class="simple-text logo-normal">
      {{ __('Creative Tim') }}
    </a>
  </div>
  <div class="sidebar-wrapper">
    <ul class="nav">
      <li class="nav-item{{ $activePage == 'oai' ? ' active' : '' }}">
        <a class="nav-link" href="{{ route('oai') }}">
          <i class="material-icons">build</i>
          <p>{{ __('Oai') }}</p>
        </a>
      </li>
      <li class="nav-item{{ $activePage == 'custom' ? ' active' : '' }}">
        <a class="nav-link" href="{{ route('custom') }}">
          <i class="material-icons">settings_applications</i>
          <p>{{ __('Custom') }}</p>
        </a>
      </li>
    </ul>
  </div>
</div>