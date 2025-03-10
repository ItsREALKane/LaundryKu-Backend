<h1>List Laundry</h1>
<h3>{{$laundry}}</h3>
<table border="1">
    <thead>
        <tr>
            <th>Nama</th>
            <th>Alamat</th>
            <th>Telepon</th>
            <th>Image</th>
            <th>Rating</th>
            <th>Jasa</th>
            <th>Created</th>
            <th>Updated</th>
        </tr>
    </thead>
    <tbody>
        @foreach($laundry as $l)
        <tr>
            <td>{{ $l->nama }}</td>
            <td>{{ $l->alamat }}</td>
            <td>{{ $l->nomor }}</td>
            <td>
                <img src="{{ $l->img }}" alt="Laundry Image" width="100">
            </td>
            <td>{{ $l->rating }}</td>
            <td>{{ $l->jasa }}</td>
            <td>{{ $l->created_at }}</td>
            <td>{{ $l->updated_at }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
