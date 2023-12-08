<div class="polls">
    <div class="container typography">
        <div class="row">
            <div class="col-12">
                <% loop $sortedPolls %>
                    <% if $isActive %>
                        <div class="poll">
                            <div class="poll__header">
                                <h2>$Title</h2>
                            </div>
                            <% if $canPoll %>
                                $PollForm
                            <% else %>
                                <div class="poll__results">
                                    <% loop $PollResults %>
                                        <div class="result">
                                            <div class="percentage bold">
                                                {$Percentage}%
                                            </div>
                                            <div class="option">
                                                <span class="bold">$Option</span>
                                                <div class="progress">
                                                    <div class="progress-bar" role="progressbar" style="width: {$Percentage}%;" aria-valuenow="{$Percentage}" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                            </div>
                                        </div>
                                    <% end_loop %>
                                </div>
                            <% end_if %>
                        </div>
                    <% end_if %>
                <% end_loop %>
            </div>
        </div>
    </div>
</div>
