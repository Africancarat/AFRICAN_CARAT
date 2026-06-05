<ul class="nav">

    <li class="nav-item">
        <a href="{{ route('seller.dashboard') }}">
            <i class="fas fa-home"></i>
            <p>{{ __('Dashboard') }}</p>
        </a>
    </li>


    <li class="nav-item">
        <a data-toggle="collapse" href="#items">
            <i class="fab fa-product-hunt"></i>
            <p>{{ __('Manage Products') }}</p>
            <span class="caret"></span>
        </a>
        <div class="collapse" id="items">
            <ul class="nav nav-collapse">
                
                <li>
                    <a class="sub-link" href="{{ route('seller.item.add') }}">
                        <span class="sub-item">{{ __('Add Product') }}</span>
                    </a>
                </li>
                <li>
                    <a class="sub-link" href="{{ route('seller.item.index') }}">
                        <span class="sub-item">{{ __('All Products') }}</span>
                    </a>
                </li>
                <li>
                    <a class="sub-link" href="{{ route('seller.item.stock.out') }}">
                        <span class="sub-item">{{ __('Stock Out Products') }}</span>
                    </a>
                </li>
               
                <li>
                    <a class="sub-link" href="{{ route('seller.bulk.product.index') }}">
                        <span class="sub-item">{{ __('CSV Import & Export') }}</span>
                    </a>
                </li>
               
            </ul>
        </div>
    </li>

    <li class="nav-item {{ request()->is('orders/*') ? 'submenu' : '' }}">
        <a data-toggle="collapse" href="#order">
            <i class="fab fa-first-order"></i>
            <p>{{ __('Manage Orders') }} </p>
            <span class="caret"></span>
        </a>
        <div class="collapse" id="order">
            <ul class="nav nav-collapse">
                <li class="{{ !request()->input('type') && request()->is('admin/orders') ? 'active' : '' }}">
                    <a class="sub-link" href="{{ route('seller.order.index') }}">
                        <span class="sub-item">{{ __('All Orders') }}</span>
                    </a>
                </li>
                <li class="{{ request()->input('type') == 'Pending' ? 'active' : '' }}">
                    <a class="sub-link" href="{{ route('seller.order.index') . '?type=' . 'Pending' }}">
                        <span class="sub-item">{{ __('Pending Orders') }}</span>
                    </a>
                </li>
                <li class="{{ request()->input('type') == 'In Progress' ? 'active' : '' }}">
                    <a class="sub-link" href="{{ route('seller.order.index') . '?type=' . 'In Progress' }}">
                        <span class="sub-item">{{ __('Progress Orders') }}</span>
                    </a>
                </li>

                <li class="{{ request()->input('type') == 'Delivered' ? 'active' : '' }}">
                    <a class="sub-link" href="{{ route('seller.order.index') . '?type=' . 'Delivered' }}">
                        <span class="sub-item">{{ __('Delivered Orders') }}</span>
                    </a>
                </li>
                <li class="{{ request()->input('type') == 'Canceled' ? 'active' : '' }}">
                    <a class="sub-link" href="{{ route('seller.order.index') . '?type=' . 'Canceled' }}">
                        <span class="sub-item">{{ __('Canceled Orders') }}</span>
                    </a>
                </li>
            </ul>
        </div>
    </li>

    <li class="nav-item">
        <a href="{{ route('seller.transaction.index') }}">
            <i class="fas fa-random"></i>
            <p>{{ __('Transactions') }}</p>
        </a>
    </li>

    <li class="nav-item">
        <a data-toggle="collapse" href="#ecommerce">
            <i class="fas fa-newspaper"></i>
            <p>{{ __('Ecommerce') }}</p>
            <span class="caret"></span>
        </a>
        <div class="collapse" id="ecommerce">
            <ul class="nav nav-collapse">
               
                <li>
                    <a class="sub-link" href="{{ route('seller.shipping.index') }}">
                        <span class="sub-item">{{ __('Shipping') }}</span>
                    </a>
                </li>


            </ul>
        </div>
    </li>


</ul>
