<div class="card bg-light mb-3" id="post_container_{{$post->id}}">
    <div class="card-header right-side-posts-header">
        <span class="username-post">{{ucfirst($post->user->fname)}}</span>
        @if(Auth::user()->id != $post->user->id || canUpdate('Discussion') || canDelete('Discussion'))
            <div class="dropdown float-right">
                <button type="button" class="btn btn-light browse-btn" data-toggle="dropdown">
                    <i class="fas fa-ellipsis-v font-weight-bold browse-icon"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-right text-left">
                    @if(Auth::user()->id != $post->user->id)
                    <a class="dropdown-item" href="{{route('messages.show', $post->user->id)}}">Send Message</a>
                    @endif
                    @if(Auth::user()->id == $post->user->id)
                        @if(canUpdate('Discussion'))
                            <a class="dropdown-item" href="JavaScript:void(0)"
                            data-toggle="modal" data-target="#req" data-type="post" data-id="{{$post->id}}" data-mode="edit">
                              Edit
                            </a>
                        @endif
                        @if(canDelete('Discussion'))
                            <a class="dropdown-item" href="JavaScript:void(0)"
                            data-toggle="modal" data-target="#confirm" data-id="{{$post->id}}" data-type="post">
                              Delete
                            </a>
                        @endif
                    @endif

                </div>
            </div>
        @endif
        <span class="lb"><small>Created at: {{$post->created_at}}</small></span>
    </div>
    <div class="card-body right-side-post-body">
        <h4 class="card-title right-side-post-btitle">
            <a class="edit_title" href="{{route('discussion.show',$post->discussion->id).'?module_order='.$post->module->module_order.'&post='.$post->id}}">{{$post->title}}</a>
        </h4>
        <div class="card-text edit_post">{{ $post->body }}</div>
        <div class="edit_image" hidden>@foreach($post->files as $file){{$file->type.';'.$file->filename}},@endforeach</div>
    </div>
    <div class="card-footer right-side-footer-card">
        <div class="row">
            <div class="col-lg-3 pl-3 mt-1">
                <span class="reply-lbl">Reply</span>
                <span class="badge badge-dark badge-pill reply-lbl-counter">{{count($post->replies)}}</span>
            </div>

        </div>
    </div>
</div>
