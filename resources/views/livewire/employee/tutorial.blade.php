<div>
    <div class="mt-5">
        <iframe width="100%" height="600" src="https://www.youtube-nocookie.com/embed/PIyPkoxdHsk?si=NzdoYwmel2BHSQZ7" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
        <hr class="my-4">
        <div class="text-uppercase my-5">
            <h4 class="fw-bold">Frequently Asked Questions</h4>
            <p>Here are the following questions that are frequently asked.</p>
        </div>
        <div class="accordion mt-5" id="employeeGuideAccordion">
            @foreach ($records as $index => $record)
                <?php
                    $collapseId = "collapse" . ($index + 1);
                    $headingId = "heading" . ($index + 1);
                ?>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="{{ $headingId }}">
                        <button class="accordion-button text-uppercase fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#{{ $collapseId }}" aria-expanded="true" aria-controls="{{ $collapseId }}">
                            {{ $record['name'] }}
                        </button>
                    </h2>
                    <div id="{{ $collapseId }}" class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}" aria-labelledby="{{ $headingId }}" data-bs-parent="#employeeGuideAccordion">
                        <div class="accordion-body">
                            {!! $record['description'] !!}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>