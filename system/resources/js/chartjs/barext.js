// Notice now we're extending the particular Line chart type, rather than the base class.
Chart.types.Bar.extend({
    // Passing in a name registers this chart in the Chart namespace in the same way
    name: "BarExt",
    initialize: function (data) {
        Chart.types.Bar.prototype.initialize.apply(this, arguments);

        var canvas = this.chart.ctx.canvas;
        var me = this;

        canvas.addEventListener('mouseup', function (evt) {
            if (me.inBox(event)) {
                me.chart.showing_values = !me.chart.showing_values;
                me.draw();
            }
        }, false);
    },
    draw: function (data) {
        Chart.types.Bar.prototype.draw.apply(this, arguments);
        var chart = this.chart,
            ctx = chart.ctx;

        this.drawBox(ctx, chart.width - 30, 10, 20, 20);

        if (chart.showing_values) {
            this.showValues();
        }
    },
    drawBox: function (ctx, x, y, width, height) {
        ctx.beginPath();
        ctx.moveTo(x, y);
        ctx.lineTo(x + width, y);
        ctx.lineTo(x + width, y + height);
        ctx.lineTo(x, y + height);
        ctx.lineTo(x, y);
        ctx.closePath();
        ctx.fillStyle = '#8DAD62';
        ctx.fill();
        ctx.strokeStyle = '#8DAD62';
        ctx.stroke();
        var img = new Image();
        img.src = $ac.site_url + 'app/view/theme/admin/image/cmd.png';

        ctx.drawImage(img, x + 2, y + 2, 16, 16);
    },
    inBox: function (event) {
        var pos = this.getMousePos(event)
        return pos.x > this.chart.width - 30 && pos.x < this.chart.width - 10 && pos.y > 10 && pos.y < 30;
    },
    getMousePos: function (event) {
        var rect = this.chart.ctx.canvas.getBoundingClientRect();
        return {
            x: event.clientX - rect.left,
            y: event.clientY - rect.top
        };
    },
    showValues: function (chart) {
        chart = chart || this;

        for (var d in chart.datasets) {
            bars = chart.datasets[d].bars;

            for (var b in bars) {
                bar = bars[b];

                new Chart.Tooltip({
                    x: bar.x,
                    y: bar.y,
                    xPadding: chart.options.tooltipXPadding,
                    yPadding: chart.options.tooltipYPadding,
                    fillColor: chart.options.tooltipFillColor,
                    textColor: chart.options.tooltipFontColor,
                    fontFamily: chart.options.tooltipFontFamily,
                    fontStyle: chart.options.tooltipFontStyle,
                    fontSize: chart.options.tooltipFontSize,
                    caretHeight: chart.options.tooltipCaretSize,
                    cornerRadius: chart.options.tooltipCornerRadius,
                    text: bar.value,
                    chart: chart.chart
                }).draw();
            }
        }
    }
});

$.fn.showValues = function (show) {
    return this.each(function (i, e) {
        var chart = $(e).data('chart');

        if (!chart) return;

        chart = chart.chart;

        if (show !== chart.chart.showing_values) {
            chart.chart.showing_values = !chart.chart.showing_values;
            chart.draw()
        }
    });
}