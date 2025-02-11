<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $form->title; ?></title>

    <style>
        .form-container {
            max-width: 700px;
            background: white;
            border-radius: 10px;
            padding: 30px;
            margin: 20px auto;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
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
        <!-- Form Title -->
        <div class="form-header text-center mb-4">
            <h2 class="fw-bold"><?= $form->title; ?></h2>
            <p class="text-muted"><?= $form->description; ?></p>
        </div>

        <!-- Display Responses -->
        <?php if (!empty($form_answers)): ?>
            <?php foreach ($form_answers as $index => $answer): ?>
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
            <p>No responses available for this form.</p>
        <?php endif; ?>

    </div>
</div>

</body>
</html>
