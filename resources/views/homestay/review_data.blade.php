
    <div class="review-container">
        @forelse($customerReviews as $customerReview)
            <div class="review">
                <h5 class="mb-0">{{$customerReview->name}}</h5>
                <div class="customer-rating">
                    @for($i = 0; $i < 5;$i++)
                        @if($i < $customerReview->review_star)
                            <span class="rated">&#9733</span>
                        @else
                            <span class="unrated">☆</span>
                        @endif
                    @endfor
                </div>
                <div>{{date('d/m/Y H:i',strtotime($customerReview->updated_at))}}</div>
                <p>{{$customerReview->review_comment}}</p>
                @if($customerReview->review_images != null)
                    <div class="img-review-container">
                        @foreach(json_decode($customerReview->review_images) as $key => $reviewImage)
                            <img src="{{URL('../'.$reviewImage)}}" alt="Customer Review Image" class="img-review" data-counter="{{$key}}">
                        @endforeach
                    </div>
                @endif
            </div>
        @empty
            <h5 class="text-center p-5">
                Tiada nilaian diberikan oleh pelanggan
            </h5>
        @endforelse
    </div>
    {{$customerReviews->links()}}  

