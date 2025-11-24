@foreach ($tours as $tour)
    <tr>
        <td>{{ $tour->title }}</td>
        <td>{{ $tour->time }}</td>
        <td>{!! Str::limit($tour->description, 100) !!}</td>
        <td>{{ $tour->quantity }}</td>
        <td>{{ number_format($tour->priceAdult, 0, ',', '.') }} VNĐ</td>
        <td>{{ number_format($tour->priceChild, 0, ',', '.') }} VNĐ</td>
        <td>{{ $tour->destination }}</td>

        <td>
            @if($tour->availability == 1)
                <span class="badge badge-success">Khả dụng</span>
            @else
                <span class="badge badge-secondary">Chưa sẵn sàng</span>
            @endif
        </td>

        <td>{{ date('d-m-Y', strtotime($tour->startDate)) }}</td>
        <td>{{ date('d-m-Y', strtotime($tour->endDate)) }}</td>

        <td>
            <!-- NÚT SỬA -->
            <button type="button"
                class="btn-action-listTours edit-tour"
                data-toggle="modal"
                data-target="#edit-tour-modal"
                data-tourid="{{ $tour->tourId }}"
                data-urledit="{{ route('admin.tour-edit') }}">
                <span class="glyphicon glyphicon-edit"
                    style="color: #26B99A; font-size:24px"></span>
            </button>
        </td>

        <td>
            <!-- NÚT XOÁ -->
            <button type="button"
                class="btn-action-listTours delete-tour"
                data-tourid="{{ $tour->tourId }}"
                data-tourname="{{ $tour->title }}">
                <span class="glyphicon glyphicon-trash"
                    style="color: red; font-size:24px"></span>
            </button>
        </td>
    </tr>
@endforeach
