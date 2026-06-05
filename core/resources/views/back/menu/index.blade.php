@extends('master.back')


@section('content')


<section class="content menu-builder-section">
    <div class="container-fluid">
    <!-- Page Heading -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-sm-flex align-items-center justify-content-between">
                <h3 class="mb-0 bc-title"><b>{{ __('Menu Builder') }}</b></h3>
                <div class="d-flex align-items-center justify-content-between">
                    <button id="updateMenu" class="btn btn-primary btn-sm mr-4">{{ __('Update Main Menu') }}</button>
                </div>
            </div>
        </div>
    </div>
        <div class="row">
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h6>{{ __('Add/Edit/Update Area') }}</h6>
                    </div>
                    <div class="card-body">
                        <form id="frmEdit" class="form-horizontal">
                            <input class="item-menu" type="hidden" name="type" value="">

                            <div id="withUrl">
                                <div class="form-group">
                                    <label for="text">{{ __('Text') }}</label>
                                    <input type="text" class="form-control item-menu" name="text" placeholder="Text">
                                </div>
                                <div class="form-group">
                                    <label for="href">{{ __('URL') }}</label>
                                    <input type="text" class="form-control item-menu" name="href" placeholder="URL">
                                </div>
                                <div class="form-group">
                                    <label for="target">{{ __('Target') }}</label>
                                    <select name="target" id="target" class="form-control item-menu">
                                        <option value="_self">{{ __('Self') }}</option>
                                        <option value="_blank">{{ __('Blank') }}</option>
                                        <option value="_top">{{ __('Top') }}</option>
                                    </select>
                                </div>
                            </div>

                            <div id="withoutUrl" style="display: none;">
                                <div class="form-group">
                                    <label for="text">{{ __('Text') }}</label>
                                    <input type="text" class="form-control item-menu" name="text" placeholder="Text">
                                </div>
                                <div class="form-group">
                                    <label for="href">{{ __('URL') }}</label>
                                    <input type="text" class="form-control item-menu" name="href" placeholder="URL">
                                </div>
                                <div class="form-group">
                                    <label for="target">{{ __('Target') }}</label>
                                    <select name="target" class="form-control item-menu">
                                        <option value="_self">{{ __('Self') }}</option>
                                        <option value="_blank">{{ __('Blank') }}</option>
                                        <option value="_top">{{ __('Top') }}</option>
                                    </select>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer">
                        <button type="button" id="btnUpdate" class="btn btn-info btn-sm" disabled><i class="fas fa-sync-alt"></i> {{ __('Update Menu') }}</button>
                        <button type="button" id="btnAdd" class="btn btn-success btn-sm"><i class="fas fa-plus"></i> {{ __('Add Menu') }}</button>
                    </div>
                </div>
                
            </div>
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h6>{{ __('Main Menu Area') }}</h6>
                    </div>
                    <div class="card-body">
                        <ul id="myEditor" class="sortableLists list-group">
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h6>{{ __('Pre-Made Menu Area') }}</h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-group pre-made-menu-list">

                            <li class="list-group-item">{{__('Home')}} <a data-text="{{__('Home')}}" data-type="home" class="addToMenus btn btn-info btn-sm float-right" href=""><i class="fas fa-plus"></i></a></li>

                            <li class="list-group-item">{{__('Shop')}}
                                <a data-text="{{__('Shop')}}" data-type="shop" class="addToMenus btn btn-info btn-sm float-right" href=""><i class="fas fa-plus"></i></a>
                            </li>

                            <li class="list-group-item">{{__('Campaign')}}
                                <a data-text="{{__('Campaign')}}" data-type="campaign" class="addToMenus btn btn-info btn-sm float-right" href=""><i class="fas fa-plus"></i></a>
                            </li>
                            

                            <li class="list-group-item">{{__('Brand')}}
                                <a data-text="{{__('Brand')}}" data-type="brand" class="addToMenus btn btn-info btn-sm float-right" href=""><i class="fas fa-plus"></i></a>
                            </li>

                            <li class="list-group-item">{{__('Blog')}}
                                <a data-text="{{__('Blog')}}" data-type="blog" class="addToMenus btn btn-info btn-sm float-right" href=""><i class="fas fa-plus"></i></a>
                            </li>

                            <li class="list-group-item">{{__('Faq')}}
                                <a data-text="{{__('Faq')}}" data-type="faq" class="addToMenus btn btn-info btn-sm float-right" href=""><i class="fas fa-plus"></i></a>
                            </li>

                            <li class="list-group-item">{{__('Contact')}}
                                <a data-text="{{__('Contact')}}" data-type="contact" class="addToMenus btn btn-info btn-sm float-right" href=""><i class="fas fa-plus"></i></a>
                            </li>

                            <li class="list-group-item">{{__('Pages')}}
                                <a data-text="{{__('Pages')}}" data-type="pages" class="addToMenus btn btn-info btn-sm float-right" href=""><i class="fas fa-plus"></i></a>
                            </li>
                
                            @foreach ($pages as $page)
                            <li class="list-group-item">
                                {{$page->title}}
                                <a data-text="{{$page->title}}" data-type="{{$page->id}}" data-custom="yes" class="addToMenus btn btn-info btn-sm float-right" href=""><i class="fas fa-plus"></i></a>
                            </li>
                            @endforeach

                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /.row -->

</section>
@endsection


@section('scripts')
<script src="{{ asset('assets/back/js/menu.js') }}"></script>

<script>
    jQuery(document).ready(function () {
 
    var arrayjson = {!! json_encode($prevMenu) !!};


    var iconPickerOptions = {searchText: "Buscar...", labelHeader: "{0}/{1}"};

    var sortableListOptions = {
        placeholderCss: {'background-color': "#ddd"}
    };

    var editor = new MenuEditor('myEditor', {listOptions: sortableListOptions, iconPicker: iconPickerOptions});
    editor.setForm($('#frmEdit'));
    editor.setUpdateButton($('#btnUpdate'));


    editor.setData({!! $prevMenu !!});
 

    $('#updateMenu').on('click', function () {
        var str = editor.getString();
        let fd = new FormData();
        fd.append('str', str);
     

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: "{{route('back.menu.update')}}",
            type: 'POST',
            data: fd,
            contentType: false,
            processData: false,
            success: function(data) {
                console.log(data);
                if(data.status == 'error') {
                    error(data.message);
                } else {
                    success(data.message);
                }
            }
        });
    });


    $("#btnUpdate").click(function(){
        disableWithoutUrl();
        editor.update();
        enableWithoutUrl();
    });

    $('#btnAdd').click(function(){
        disableWithoutUrl();
        $("input[name='type']").val('custom');
        editor.add();
        enableWithoutUrl();
    });


    $(".addToMenus").on('click', function(e) {
        e.preventDefault();
    
        $("input[name='type']").val($(this).data('type'));
        $("#withoutUrl input[name='text']").val($(this).data('text'));
        $("#withoutUrl input[name='target']").val('_self');
        editor.add();
       

        if ($(this).data('type').indexOf('mega') > -1) {
            $("#myEditor").find("span.txt").last().after(" <span class='ml-2 badge badge-danger'>Mega Menu</span>");
        }

    });


});
</script>
@endsection
