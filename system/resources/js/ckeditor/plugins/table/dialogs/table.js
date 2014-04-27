﻿/*
 Copyright (c) 2003-2011, CKSource - Frederico Knabben. All rights reserved.
 For licensing, see LICENSE.html or http://ckeditor.com/license
 */

(function () {
    var a = /^(\d+(?:\.\d+)?)(px|%)$/, b = /^(\d+(?:\.\d+)?)px$/, c = function (e) {
        var f = this.id;
        if (!e.info)e.info = {};
        e.info[f] = this.getValue();
    };

    function d(e, f) {
        var g = function (i) {
            return new CKEDITOR.dom.element(i, e.document);
        }, h = e.plugins.dialogadvtab;
        return{title: e.lang.table.title, minWidth: 310, minHeight: CKEDITOR.env.ie ? 310 : 280, onLoad: function () {
            var i = this, j = i.getContentElement('advanced', 'advStyles');
            if (j)j.on('change', function (k) {
                var l = this.getStyle('width', ''), m = i.getContentElement('info', 'txtWidth'), n = i.getContentElement('info', 'cmbWidthType'), o = 1;
                if (l) {
                    o = l.length < 3 || l.substr(l.length - 1) != '%';
                    l = parseInt(l, 10);
                }
                m && m.setValue(l, true);
                n && n.setValue(o ? 'pixels' : 'percents', true);
                var p = this.getStyle('height', ''), q = i.getContentElement('info', 'txtHeight');
                p && (p = parseInt(p, 10));
                q && q.setValue(p, true);
            });
        }, onShow: function () {
            var q = this;
            var i = e.getSelection(), j = i.getRanges(), k = null, l = q.getContentElement('info', 'txtRows'), m = q.getContentElement('info', 'txtCols'), n = q.getContentElement('info', 'txtWidth'), o = q.getContentElement('info', 'txtHeight');
            if (f == 'tableProperties') {
                if (k = i.getSelectedElement())k = k.getAscendant('table', true); else if (j.length > 0) {
                    if (CKEDITOR.env.webkit)j[0].shrink(CKEDITOR.NODE_ELEMENT);
                    var p = j[0].getCommonAncestor(true);
                    k = p.getAscendant('table', true);
                }
                q._.selectedElement = k;
            }
            if (k) {
                q.setupContent(k);
                l && l.disable();
                m && m.disable();
            } else {
                l && l.enable();
                m && m.enable();
            }
            n && n.onChange();
            o && o.onChange();
        }, onOk: function () {
            var D = this;
            if (D._.selectedElement)var i = e.getSelection(), j = i.createBookmarks();
            var k = D._.selectedElement || g('table'), l = D, m = {};
            D.commitContent(m, k);
            if (m.info) {
                var n = m.info;
                if (!D._.selectedElement) {
                    var o = k.append(g('tbody')), p = parseInt(n.txtRows, 10) || 0, q = parseInt(n.txtCols, 10) || 0;
                    for (var r = 0; r < p; r++) {
                        var s = o.append(g('tr'));
                        for (var t = 0; t < q; t++) {
                            var u = s.append(g('td'));
                            if (!CKEDITOR.env.ie)u.append(g('br'));
                        }
                    }
                }
                var v = n.selHeaders;
                if (!k.$.tHead && (v == 'row' || v == 'both')) {
                    var w = new CKEDITOR.dom.element(k.$.createTHead());
                    o = k.getElementsByTag('tbody').getItem(0);
                    var x = o.getElementsByTag('tr').getItem(0);
                    for (r = 0; r < x.getChildCount(); r++) {
                        var y = x.getChild(r);
                        if (y.type == CKEDITOR.NODE_ELEMENT && !y.data('cke-bookmark')) {
                            y.renameNode('th');
                            y.setAttribute('scope', 'col');
                        }
                    }
                    w.append(x.remove());
                }
                if (k.$.tHead !== null && !(v == 'row' || v == 'both')) {
                    w = new CKEDITOR.dom.element(k.$.tHead);
                    o = k.getElementsByTag('tbody').getItem(0);
                    var z = o.getFirst();
                    while (w.getChildCount() > 0) {
                        x = w.getFirst();
                        for (r = 0; r < x.getChildCount(); r++) {
                            var A = x.getChild(r);
                            if (A.type == CKEDITOR.NODE_ELEMENT) {
                                A.renameNode('td');
                                A.removeAttribute('scope');
                            }
                        }
                        x.insertBefore(z);
                    }
                    w.remove();
                }
                if (!D.hasColumnHeaders && (v == 'col' || v == 'both'))for (s = 0; s < k.$.rows.length; s++) {
                    A = new CKEDITOR.dom.element(k.$.rows[s].cells[0]);
                    A.renameNode('th');
                    A.setAttribute('scope', 'row');
                }
                if (D.hasColumnHeaders && !(v == 'col' || v == 'both'))for (r = 0; r < k.$.rows.length; r++) {
                    s = new CKEDITOR.dom.element(k.$.rows[r]);
                    if (s.getParent().getName() == 'tbody') {
                        A = new CKEDITOR.dom.element(s.$.cells[0]);
                        A.renameNode('td');
                        A.removeAttribute('scope');
                    }
                }
                var B = [];
                if (n.txtHeight)k.setStyle('height', CKEDITOR.tools.cssLength(n.txtHeight)); else k.removeStyle('height');
                if (n.txtWidth) {
                    var C = n.cmbWidthType || 'pixels';
                    k.setStyle('width', n.txtWidth + (C == 'pixels' ? 'px' : '%'));
                } else k.removeStyle('width');
                if (!k.getAttribute('style'))k.removeAttribute('style');
            }
            if (!D._.selectedElement)e.insertElement(k); else i.selectBookmarks(j);
            return true;
        }, contents: [
            {id: 'info', label: e.lang.table.title, elements: [
                {type: 'hbox', widths: [null, null], styles: ['vertical-align:top'], children: [
                    {type: 'vbox', padding: 0, children: [
                        {type: 'text', id: 'txtRows', 'default': 3, label: e.lang.table.rows, required: true, style: 'width:5em', validate: function () {
                            var i = true, j = this.getValue();
                            i = i && CKEDITOR.dialog.validate.integer()(j) && j > 0;
                            if (!i) {
                                alert(e.lang.table.invalidRows);
                                this.select();
                            }
                            return i;
                        }, setup: function (i) {
                            this.setValue(i.$.rows.length);
                        }, commit: c},
                        {type: 'text', id: 'txtCols', 'default': 2, label: e.lang.table.columns, required: true, style: 'width:5em', validate: function () {
                            var i = true, j = this.getValue();
                            i = i && CKEDITOR.dialog.validate.integer()(j) && j > 0;
                            if (!i) {
                                alert(e.lang.table.invalidCols);
                                this.select();
                            }
                            return i;
                        }, setup: function (i) {
                            this.setValue(i.$.rows[0].cells.length);
                        }, commit: c},
                        {type: 'html', html: '&nbsp;'},
                        {type: 'select', id: 'selHeaders', 'default': '', label: e.lang.table.headers, items: [
                            [e.lang.table.headersNone, ''],
                            [e.lang.table.headersRow, 'row'],
                            [e.lang.table.headersColumn, 'col'],
                            [e.lang.table.headersBoth, 'both']
                        ], setup: function (i) {
                            var j = this.getDialog();
                            j.hasColumnHeaders = true;
                            for (var k = 0; k < i.$.rows.length; k++) {
                                if (i.$.rows[k].cells[0].nodeName.toLowerCase() != 'th') {
                                    j.hasColumnHeaders = false;
                                    break;
                                }
                            }
                            if (i.$.tHead !== null)this.setValue(j.hasColumnHeaders ? 'both' : 'row'); else this.setValue(j.hasColumnHeaders ? 'col' : '');
                        }, commit: c},
                        {type: 'text', id: 'txtBorder', 'default': 1, label: e.lang.table.border, style: 'width:3em', validate: CKEDITOR.dialog.validate.number(e.lang.table.invalidBorder), setup: function (i) {
                            this.setValue(i.getAttribute('border') || '');
                        }, commit: function (i, j) {
                            if (this.getValue())j.setAttribute('border', this.getValue());
                            else j.removeAttribute('border');
                        }},
                        {id: 'cmbAlign', type: 'select', 'default': '', label: e.lang.common.align, items: [
                            [e.lang.common.notSet, ''],
                            [e.lang.common.alignLeft, 'left'],
                            [e.lang.common.alignCenter, 'center'],
                            [e.lang.common.alignRight, 'right']
                        ], setup: function (i) {
                            this.setValue(i.getAttribute('align') || '');
                        }, commit: function (i, j) {
                            if (this.getValue())j.setAttribute('align', this.getValue()); else j.removeAttribute('align');
                        }}
                    ]},
                    {type: 'vbox', padding: 0, children: [
                        {type: 'hbox', widths: ['5em'], children: [
                            {type: 'text', id: 'txtWidth', style: 'width:5em', label: e.lang.common.width, 'default': 500, validate: CKEDITOR.dialog.validate.number(e.lang.table.invalidWidth), onLoad: function () {
                                var i = this.getDialog().getContentElement('info', 'cmbWidthType'), j = i.getElement(), k = this.getInputElement(), l = k.getAttribute('aria-labelledby');
                                k.setAttribute('aria-labelledby', [l, j.$.id].join(' '));
                            }, onChange: function () {
                                var i = this.getDialog().getContentElement('advanced', 'advStyles');
                                if (i) {
                                    var j = this.getValue();
                                    if (j)j += this.getDialog().getContentElement('info', 'cmbWidthType').getValue() == 'percents' ? '%' : 'px';
                                    i.updateStyle('width', j);
                                }
                            }, setup: function (i) {
                                var j = a.exec(i.$.style.width);
                                if (j)this.setValue(j[1]); else this.setValue('');
                            }, commit: c},
                            {id: 'cmbWidthType', type: 'select', label: e.lang.table.widthUnit, labelStyle: 'visibility:hidden', 'default': 'pixels', items: [
                                [e.lang.table.widthPx, 'pixels'],
                                [e.lang.table.widthPc, 'percents']
                            ], setup: function (i) {
                                var j = a.exec(i.$.style.width);
                                if (j)this.setValue(j[2] == 'px' ? 'pixels' : 'percents');
                            }, onChange: function () {
                                this.getDialog().getContentElement('info', 'txtWidth').onChange();
                            }, commit: c}
                        ]},
                        {type: 'hbox', widths: ['5em'], children: [
                            {type: 'text', id: 'txtHeight', style: 'width:5em', label: e.lang.common.height, 'default': '', validate: CKEDITOR.dialog.validate.number(e.lang.table.invalidHeight), onLoad: function () {
                                var i = this.getDialog().getContentElement('info', 'htmlHeightType'), j = i.getElement(), k = this.getInputElement(), l = k.getAttribute('aria-labelledby');
                                k.setAttribute('aria-labelledby', [l, j.$.id].join(' '));
                            }, onChange: function () {
                                var i = this.getDialog().getContentElement('advanced', 'advStyles');
                                if (i) {
                                    var j = this.getValue();
                                    i.updateStyle('height', j && j + 'px');
                                }
                            }, setup: function (i) {
                                var j = b.exec(i.$.style.height);
                                if (j)this.setValue(j[1]);
                            }, commit: c},
                            {id: 'htmlHeightType', type: 'html', html: '<div><br />' + e.lang.table.widthPx + '</div>'}
                        ]},
                        {type: 'html', html: '&nbsp;'},
                        {type: 'text', id: 'txtCellSpace', style: 'width:3em', label: e.lang.table.cellSpace, 'default': 1, validate: CKEDITOR.dialog.validate.number(e.lang.table.invalidCellSpacing), setup: function (i) {
                            this.setValue(i.getAttribute('cellSpacing') || '');
                        }, commit: function (i, j) {
                            if (this.getValue())j.setAttribute('cellSpacing', this.getValue()); else j.removeAttribute('cellSpacing');
                        }},
                        {type: 'text', id: 'txtCellPad', style: 'width:3em', label: e.lang.table.cellPad, 'default': 1, validate: CKEDITOR.dialog.validate.number(e.lang.table.invalidCellPadding), setup: function (i) {
                            this.setValue(i.getAttribute('cellPadding') || '');
                        }, commit: function (i, j) {
                            if (this.getValue())j.setAttribute('cellPadding', this.getValue()); else j.removeAttribute('cellPadding');
                        }}
                    ]}
                ]},
                {type: 'html', align: 'right', html: ''},
                {type: 'vbox', padding: 0, children: [
                    {type: 'text', id: 'txtCaption', label: e.lang.table.caption, setup: function (i) {
                        var j = i.getElementsByTag('caption');
                        if (j.count() > 0) {
                            var k = j.getItem(0);
                            k = CKEDITOR.tools.trim(k.getText());
                            this.setValue(k);
                        }
                    }, commit: function (i, j) {
                        var k = this.getValue(), l = j.getElementsByTag('caption');
                        if (k) {
                            if (l.count() > 0) {
                                l = l.getItem(0);
                                l.setHtml('');
                            } else {
                                l = new CKEDITOR.dom.element('caption', e.document);
                                if (j.getChildCount())l.insertBefore(j.getFirst()); else l.appendTo(j);
                            }
                            l.append(new CKEDITOR.dom.text(k, e.document));
                        } else if (l.count() > 0)for (var m = l.count() - 1; m >= 0; m--)l.getItem(m).remove();
                    }},
                    {type: 'text', id: 'txtSummary', label: e.lang.table.summary, setup: function (i) {
                        this.setValue(i.getAttribute('summary') || '');
                    }, commit: function (i, j) {
                        if (this.getValue())j.setAttribute('summary', this.getValue()); else j.removeAttribute('summary');
                    }}
                ]}
            ]},
            h && h.createAdvancedTab(e)
        ]};
    };
    CKEDITOR.dialog.add('table', function (e) {
        return d(e, 'table');
    });
    CKEDITOR.dialog.add('tableProperties', function (e) {
        return d(e, 'tableProperties');
    });
})();