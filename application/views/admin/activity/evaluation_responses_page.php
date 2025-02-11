<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($form) && $form ? $form->title : 'Form Not Found'; ?></title>

    <style>
        .form-container {
            max-width: 700px !important;
            background: white !important;
            border-radius: 10px !important;
            padding: 30px !important;
            margin: 20px auto !important;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1) !important;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .rating-container {
            display: flex;
            gap: 5px;
        }

        .star {
            font-size: 30px;
            color: #ccc;
        }

        .star.selected {
            color: gold;
        }
    </style>
</head>
<body>

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

</body>
</html>
