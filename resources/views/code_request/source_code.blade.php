@extends('layouts.master')

@section('css')
<style>
    textarea {
        width: 100%;
        min-height: 15vh;
        
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 16px;
        resize: none;
        overflow-y: hidden;
        transition: height 0.3s ease;
    }

    @media (min-width: 992px) {
        textarea {
            max-height: 75vh; /* Set max height to 90% of viewport height for larger screens */
        }
    }
</style>
@endsection

@section('content')
<div class="container">
    <div class="row">
        <!-- Problem Description -->
        <div class="col-lg-6 col-12 textarea-wrapper">
            <h4>Problem Description</h4>
            <textarea id="problem-description">{{ $req->problem_description }}</textarea>
        </div>

        <!-- Source Code -->
        <div class="col-lg-6 col-12 textarea-wrapper">
            <h4>Source Code</h4>
            <textarea id="source-code">{{ $req->source_code }}</textarea>
        </div>
    </div>
</div>

<script>
function initTextarea(textareaId) {
    const textarea = document.getElementById(textareaId);
    autoResize(textarea);
}

function autoResize(textarea) {
    textarea.style.height = 'auto';
    textarea.style.height = (textarea.scrollHeight + 100) + 'px';
}

// Initialize textareas with content
document.addEventListener('DOMContentLoaded', function() {
    initTextarea('problem-description');
    initTextarea('source-code');
});
</script>

@endsection
