<div class="container">
    <div class="form-container">
        <!-- Display Form Title and Description -->
        <?php if (isset($form) && $form): ?>
            <h2 style="text-align: center;"><?= $form->title; ?></h2>
            <p style="text-align: center;"><?= $form->description; ?></p>
        <?php else: ?>
            <p style="text-align: center;">Form not found or unavailable.</p>
        <?php endif; ?>


        <!-- Display Answers -->
        <?php if (!empty($answers)): ?>
            <?php foreach ($answers as $index => $answer): ?>
                <div class="form-group">
                    <label><strong><?= ($index + 1) . '. ' . $answer->label; ?></strong></label>

                    <?php if ($answer->type == 'short' || $answer->type == 'textarea'): ?>
                        <p class="border p-2"><?= !empty($answer->answer) ? $answer->answer : '<em>No response</em>'; ?></p>

                    <?php elseif ($answer->type == 'rating'): ?>
                        <div class="rating-container">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <span class="star <?= ($i <= $answer->answer) ? 'selected' : ''; ?>">&#9733;</span>
                            <?php endfor; ?>
                        </div>

                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No responses available for this evaluation.</p>
        <?php endif; ?>
    </div>
</div>