@foreach($datas as $data)
    <tr>
        <td>
            <img src="{{ $data->photo ? url('/core/public/storage/images/'.$data->photo) : url('/core/public/storage/images/placeholder.png') }}" alt="Image Not Found">
        </td>
        <td>
            {{ $data->title }}
        </td>

        <td>
            <div class="action-list">
                <a class="btn btn-secondary btn-sm "
                    href="{{ route('back.service.edit',$data->id) }}">
                    <i class="fas fa-edit"></i>
                </a>
                <a class="btn btn-danger btn-sm " data-toggle="modal"
                    data-target="#confirm-delete" href="javascript:;"
                    data-href="{{ route('back.service.destroy',$data->id) }}">
                    <i class="fas fa-trash-alt"></i>
                </a>
            </div>
        </td>
    </tr>
@endforeach
