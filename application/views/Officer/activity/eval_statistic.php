<div id="pdf-content">

    <div class="card mb-3 mb-lg-0">
        <div class="card-header bg-body-tertiary d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Evaluation Statistic</h5>
            <a href="#" class="btn btn-sm btn-falcon-default ms-2" title="Download PDF" onclick="downloadPageAsPDF()" target="_blank">
                <span class="fas fa-download"></span>
            </a>

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
                                <td>1</td>
                                <td>Poor</td>
                                <td style="color: red;">🔴 Red</td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>Needs Improvement</td>
                                <td style="color: orange;">🟠 Orange</td>
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
                        </tbody>
                    </table>
                </div>

                <!-- Rating Questions Summary -->
                <div class="section-container mb-4">
                    <h4>Rating Questions Summary</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-primary">
                                <tr>
                                    <th>Question</th>
                                    <th>Average Rating</th>
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
                                        <td><?= $rating['four_star'] ?>%</td>
                                        <td><?= $rating['three_star'] ?>%</td>
                                        <td><?= $rating['two_star'] ?>%</td>
                                        <td><?= $rating['one_star'] ?>%</td>
                                        <td><strong><?= $rating['total_responses'] ?></strong></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <?php
                            // Define dynamic rating label and color based on 1-4 scale
                            $rating_text = '';
                            $rating_icon = '';
                            $rating_color = '';

                            if ($overall_rating >= 4) {
                                $rating_text = 'Good';
                                $rating_icon = '🟢';
                                $rating_color = 'lightgreen';
                            } elseif ($overall_rating >= 3) {
                                $rating_text = 'Average';
                                $rating_icon = '🟡';
                                $rating_color = 'yellow';
                            } elseif ($overall_rating >= 2) {
                                $rating_text = 'Needs Improvement';
                                $rating_icon = '🟠';
                                $rating_color = 'orange';
                            } else {
                                $rating_text = 'Poor';
                                $rating_icon = '🔴';
                                $rating_color = 'red';
                            }
                            ?>

                            <tfoot>
                                <tr class="fw-bold" style="background-color: #f8f9fa;">
                                    <td colspan="5" class="text-end">Overall Average Rating:</td>
                                    <td colspan="2" style="font-size: 1.2em; color: <?= $rating_color ?>;">
                                        <?= number_format($overall_rating, 2) ?> (<?= $rating_icon ?> <?= $rating_text ?>)
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
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

</div>


<!-- html2pdf Library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<!-- Export Function -->
<script>
    function downloadPageAsPDF() {
        const element = document.getElementById('pdf-content');

        const opt = {
            margin: 0.5,
            filename: 'evaluation_statistics.pdf',
            image: {
                type: 'jpeg',
                quality: 0.98
            },
            html2canvas: {
                scale: 2
            },
            jsPDF: {
                unit: 'in',
                format: 'letter',
                orientation: 'portrait'
            }
        };

        // Call html2pdf and get the PDF as a Blob
        html2pdf().set(opt).from(element).output('blob').then(function(pdfBlob) {
            // Create a link to open the PDF in a new tab
            const pdfUrl = URL.createObjectURL(pdfBlob);
            window.open(pdfUrl, '_blank');
        });
    }
</script>