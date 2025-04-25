<div class="card mb-3 mb-lg-0">
    <div class="card-header bg-body-tertiary d-flex justify-content-between">
        <h5 class="mb-0">Evaluation Statistic</h5>
    </div>
</div>

<!-- Space Between Sections -->
<div class="space" style="height: 20px;"></div> <!-- Adds spacing between sections -->

<div class="row gx-3">
    <div class="col-xxl-10 col-xl-12 mx-auto">
        <div class="card p-4">

            <!-- Total Attendees vs. Respondents -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="progress-card bg-light p-3 rounded">
                        <div class="progress-label mb-2">👥 Total Attendees: <strong><?= $total_attendees ?></strong></div>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: <?= $respondent_percentage ?>%;"> <?= $respondent_percentage ?>%</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mt-3 mt-md-0">
                    <div class="progress-card bg-light p-3 rounded">
                        <div class="progress-label mb-2">📊 Total Respondents: <strong><?= $total_respondents ?></strong></div>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: <?= $respondent_percentage ?>%;"> <?= $respondent_percentage ?>%</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Rating Scale Reference -->
            <div class="section-container mb-4">
                <h4>Rating Scale Reference</h4>
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>Average Rating</th>
                            <th>Category</th>
                            <th>Color Indication</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1 - 2</td>
                            <td>Needs Improvement</td>
                            <td style="color: red;">🔴 Red</td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td>Average</td>
                            <td style="color: yellow;">🟡 Yellow</td>
                        </tr>
                        <tr>
                            <td>4</td>
                            <td>Good</td>
                            <td style="color: lightgreen;">🟢 Light Green</td>
                        </tr>
                        <tr>
                            <td>5</td>
                            <td>Excellent</td>
                            <td style="color: green;">🟢 Green</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Rating Questions Summary -->
            <div class="section-container mb-4">
                <h4>Rating Questions Summary</h4>
                <table class="table table-bordered table-hover">
                    <thead class="table-primary">
                        <tr>
                            <th>Question</th>
                            <th>Average Rating</th>
                            <th>5★</th>
                            <th>4★</th>
                            <th>3★</th>
                            <th>2★</th>
                            <th>1★</th>
                            <th>Total Responses</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rating_summary as $rating): ?>
                            <tr>
                                <td><?= $rating['question'] ?></td>
                                <td><?= number_format($rating['avg_rating'], 2) ?></td>
                                <td><?= $rating['five_star'] ?>%</td>
                                <td><?= $rating['four_star'] ?>%</td>
                                <td><?= $rating['three_star'] ?>%</td>
                                <td><?= $rating['two_star'] ?>%</td>
                                <td><?= $rating['one_star'] ?>%</td>
                                <td><strong><?= $rating['total_responses'] ?></strong></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr class="fw-bold" style="background-color: #f8f9fa;">
                            <td colspan="5" class="text-end">Overall Average Rating:</td>
                            <td colspan="2" style="font-size: 1.2em; color: green;">
                                <?= number_format($overall_rating, 2) ?> (🟢 Excellent)
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Long Answer Questions Summary -->
            <div class="section-container mb-4">
                <h4>Long Answer Responses Summary</h4>
                <table class="table table-bordered">
                    <thead class="table-secondary">
                        <tr>
                            <th>Question</th>
                            <th>Responses</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($answer_summary as $long_answer): ?>
                            <tr>
                                <td><?= $long_answer['question'] ?></td>
                                <td>
                                    <ul class="mb-0">
                                        <?php foreach (explode("||", $long_answer['answers']) as $answer): ?>
                                            <li><?= $answer ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>