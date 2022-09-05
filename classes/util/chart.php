<?php

namespace local_marketplace\util;

class chart {
    public function get_chart_line() {
        $labelsdata = ['Jan', 'Feb', 'Mar', 'Apr', 'Mai', 'Jun'];
        $seriesdata = [200, 300, 150, 200, 500, 635];

        $series = new \core\chart_series('Purchases by month', $seriesdata);
        $series->set_type(\core\chart_series::TYPE_LINE);

        $chart = new \core\chart_line();
        $chart->set_smooth(true);
        $chart->add_series($series);
        $chart->set_labels($labelsdata);

        return $chart;
    }

    public function get_chart_pie() {
        $products = ['Product 1', 'Product 2', 'Product 3', 'Product 4'];
        $sales = [10, 45, 20, 25];

        $series = new \core\chart_series('Top sellers', $sales);

        $chart = new \core\chart_pie();
        $chart->add_series($series);
        $chart->set_labels($products);

        return $chart;
    }
}