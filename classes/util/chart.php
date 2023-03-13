<?php

namespace local_marketplace\util;

use local_marketplace\local\entities\order;

class chart {
    public function get_chart_line() {
        $orderentity = new order();

        $orders = $orderentity->get_ordered_products_by_month_with_quantity();

        if (!$orders) {
            return false;
        }

        $months = explode(',', get_string('months', 'local_marketplace'));

        $labelsdata = [];
        $seriesdata = [];
        foreach ($orders as $order) {
            $labelsdata[] = $months[$order->month - 1];
            $seriesdata[] = $order->quantity;
        }

        $series = new \core\chart_series('Purchases by month', $seriesdata);
        $series->set_type(\core\chart_series::TYPE_LINE);

        $chart = new \core\chart_line();
        $chart->set_smooth(true);
        $chart->add_series($series);
        $chart->set_labels($labelsdata);

        return $chart;
    }

    public function get_chart_pie() {
        $orderentity = new order();

        $orders = $orderentity->get_ordered_products_with_quantity();

        if (!$orders) {
            return false;
        }

        $products = [];
        $sales = [];
        foreach ($orders as $order) {
            $products[] = $order->name;
            $sales[] = $order->quantity;
        }

        $series = new \core\chart_series(get_string('total', 'local_marketplace'), $sales);

        $chart = new \core\chart_pie();
        $chart->add_series($series);
        $chart->set_labels($products);

        return $chart;
    }
}