<section id="user-review" aria-labelledby="Review Section">
    <h3 class="color-dark-purple mb-1"><i class="fas fa-comments"></i>&nbsp;&nbsp;Nilaian Pelanggan</h3>
    @if(count($customerReviews) > 0)
        <h4 class="color-dark-purple">            <span class="rated">
                {{$customerReviewsRating}} &#9733
            </span>({{count($customerReviews)}} Nilaian)

        </h4>
    @endif
    <div class="review-container">
        @forelse($customerReviews as $customerReview)
            <div>
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
            </div>
        @empty
            <h5 class="text-center p-5">
                Tiada nilaian diberikan oleh pelanggan
            </h5>
        @endforelse
        {{$customerReviews->links()}}        
    </div>

</section>