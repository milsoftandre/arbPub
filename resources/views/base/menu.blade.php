<div class="aside-menu flex-column-fluid">
    <div class="hover-scroll-overlay-y my-5 my-lg-5" id="kt_aside_menu_wrapper" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-height="auto" data-kt-scroll-dependencies="#kt_aside_logo, #kt_aside_footer" data-kt-scroll-wrappers="#kt_aside_menu" data-kt-scroll-offset="0">
        <div class="menu menu-column menu-title-gray-800 menu-state-title-primary menu-state-icon-primary menu-state-bullet-primary menu-arrow-gray-500" id="#kt_aside_menu" data-kt-menu="true">
            <div class="menu-item">
                <div class="menu-content pb-2">
                    <span class="menu-section text-muted text-uppercase fs-8 ls-1">User place</span>
                </div>
            </div>
            <div class="menu-item">
                <a class="menu-link {{ (!Route::currentRouteName())?'active':'' }}" href="{{ route('dashboard') }}">
										<span class="menu-icon">
											<i class="fas fa-tachometer-alt"></i>
										</span>
                    <span class="menu-title">Dashboard</span>
                </a>
            </div>
            @if (Auth::user()->type!='1')
            <div class="menu-item">
                <a class="menu-link {{ (strpos("-".Route::currentRouteName(),"exchange")&&!strpos("-".Route::currentRouteName(),"exchange-settings"))?'active':'' }}" href="{{ route('exchange.index') }}">
										<span class="menu-icon">
											<i class="fas fa-road"></i>
										</span>
                    <span class="menu-title">Exchanges</span>
                </a>
            </div>
            <div class="menu-item">
                <a class="menu-link {{ (strpos("-".Route::currentRouteName(),"currency-pairs"))?'active':'' }}" href="{{ route('currency-pairs.index') }}">
										<span class="menu-icon">
											<i class="fas fa-cube"></i>
										</span>
                    <span class="menu-title">Currency pairs</span>
                </a>
            </div>
            @endif
            <div class="menu-item">
                <a class="menu-link {{ (strpos("-".Route::currentRouteName(),"exchange-settings"))?'active':'' }}" href="{{ route('exchange-settings.index') }}">
										<span class="menu-icon">
											<i class="fas fa-list-ul"></i>
										</span>
                    <span class="menu-title">Accounts</span>
                </a>
            </div>
            <div class="menu-item">
                <a class="menu-link {{ (strpos("-".Route::currentRouteName(),"bot"))?'active':'' }}" href="{{ route('bot.index') }}">
										<span class="menu-icon">
											<i class="fas fa-robot"></i>
										</span>
                    <span class="menu-title">Bots</span>
                </a>
            </div>
            <div class="menu-item">
                <a class="menu-link {{ (strpos("-".Route::currentRouteName(),"trade-history"))?'active':'' }}" href="{{ route('trade-history.index') }}">
										<span class="menu-icon">
											<i class="fas fa-bookmark"></i>
										</span>
                    <span class="menu-title">Trade History</span>
                </a>
            </div>

            @if (Auth::user()->type!='1')

            <div class="menu-item">
                <div class="menu-content pt-8 pb-2">
                    <span class="menu-section text-muted text-uppercase fs-8 ls-1">General</span>
                </div>
            </div>
                <div class="menu-item">
                    <a class="menu-link {{ (strpos("-".Route::currentRouteName(),"employee"))?'active':'' }}" href="{{ route('employee.index') }}">
										<span class="menu-icon">
											<i class="fas fa-users"></i>
										</span>
                        <span class="menu-title">Employee </span>
                    </a>
                </div>
                <div class="menu-item">
                    <a class="menu-link {{ (strpos("-".Route::currentRouteName(),"client"))?'active':'' }}" href="{{ route('client.index') }}">
										<span class="menu-icon">
											<i class="fas fa-user-check"></i>
										</span>
                        <span class="menu-title">Users</span>
                    </a>
                </div>

            @endif

        </div>
    </div>
</div>
