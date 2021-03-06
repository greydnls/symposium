<h3><a href="{{ route('bios.show', ['id' => $bio->id]) }}">{{ $bio->nickname }}</a></h3>
<p>{{ $bio->preview }}</p>

<div class="modal fade bio-modal" id="modal-{{ $bio->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Copy bio</h4>
            </div>
            <div class="modal-body">
                <textarea class="select-me" style="width: 100%; height: 10em;">{{ $bio->body }}</textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<p>
    <a href="{{ route('bios.delete', ['id' => $bio->id]) }}" data-confirm="Are you sure you want to delete this bio?"><button type="button" class="btn btn-xs btn-danger">Delete</button></a>
    <a href="{{ route('bios.edit', ['id' => $bio->id]) }}"><button type="button" class="btn btn-xs btn-default">Edit</button></a>
    <a href="#" data-toggle="modal" data-target="#modal-{{ $bio->id }}"><button type="button" class="btn btn-xs btn-default">Copy</button></a>
</p>
