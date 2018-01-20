<?php
/**
 * News data installation script
 *
 * @author Magento
 */

/**
 *  @var $installer Mage_Core_Model_Resource_Setup
 */
$installer = $this;

/**
 * @var $model Magentostudy_News_Model_News
 */
$model = Mage::getModel('epicor_faqs/faqs');
/*
// Set up data rows
$dataRows = array(
    array(
        'weight'          => '1',
        'question'         => 'What is the airspeed velocity of an unladen swallow?',
        'answer'   => 'Averaging the above numbers and plugging them in to the Strouhal equation for cruising flight (fA/U = 7 beats per second * 0.18 meters per beat / 9.5 meters per second) yields a Strouhal number of roughly 0.13: <br> 
            ... indicating a surprisingly efficient flight pattern falling well below the expected range of 0.2–0.4.

Although a definitive answer would of course require further measurements, published species-wide averages of wing length and body mass, initial Strouhal estimates based on those averages and cross-species comparisons, the Lund wind tunnel study of birds flying at a range of speeds, and revised Strouhal numbers based on that study all lead me to estimate that the average cruising airspeed velocity of an unladen European Swallow is roughly 11 meters per second, or 24 miles an hour.',
        'stores'        => '1',
    ),

        array(
        'weight'          => '2',
        'question'         => 'What is the capital of Assyria?',
        'answer'   => 'The four capitals of Assyria were Ashur (or Qalat Sherqat), Calah (or Nimrud), the short-lived Dur Sharrukin (or Khorsabad), and Nineveh.8 The ruins of all four ancient cities fall within the modern state of Iraq.',
        'stores'        => '1,3',
    )
);

// Generate news items
foreach ($dataRows as $data) {
    $model->setData($data)->setOrigData()->save();
}

 */