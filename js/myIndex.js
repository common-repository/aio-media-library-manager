(function() {
    'use strict';
    window._AimolSmack_attachmentTaxonomiesExtendMediaLibrary = function(wpMedia, $, taxonomies) {
        // Extend wp.media.view.AttachmentFilters with a custom filter called Taxonomy.
        wpMedia.view.AttachmentFilters.Taxonomy = wpMedia.view.AttachmentFilters.extend({
            id: 'media-attachment-taxonomy-filters',
            createFilters: function() {
                const filters = {};
                
                // Check for queryVar and allLabel options.
                if (this.options.queryVar && this.options.allLabel) {
                    // Create an "all" filter option.
                    filters.all = {
                        text: this.options.allLabel,
                        props: {},
                        priority: 1
                    };
                    filters.all.props[this.options.queryVar] = null;

                        // // Create an "Uncategorized" filter option.
                        // filters.uncategorized = {
                        //     text: 'Uncategorized',
                        //     props: {},
                        //     priority: 2
                        // };
                        // filters.uncategorized.props[this.options.queryVar] = 'unc';

                    // Create filters for each term provided in the options.
                    if (this.options.terms && this.options.terms.length) {
                        for (const i in this.options.terms) {
                            const term = this.options.terms[i];
                            filters[term.slug] = {
                                text: term.name,
                                props: {},
                                priority: parseInt(i) + 3
                            };
                            filters[term.slug].props[this.options.queryVar] = term.slug;
                        }
                    }
                }

                // Assign the filters to this.filters.
                this.filters = filters;
            }
        });

        // Extend wp.media.view.AttachmentsBrowser with custom toolbar functionality.
        wpMedia.view.AttachmentsBrowser = function(attachmentsBrowser, taxonomyFilter, Label, taxonomies) {
            return attachmentsBrowser.extend({
                createToolbar: function() {
                    // Call the createToolbar method of the superclass if it exists.
                    if (attachmentsBrowser.__super__ && attachmentsBrowser.__super__.createToolbar) {
                        attachmentsBrowser.__super__.createToolbar.apply(this, arguments);
                    } else {
                        attachmentsBrowser.prototype.createToolbar.apply(this, arguments);
                    }

                    // Do not render filters in gallery editing mode.
                    const state = this.controller.state();
                    if (state.id && state.id === 'gallery-edit') {
                        return;
                    }

                    // Set up custom toolbar items for each taxonomy.
                    const data = taxonomies.data;
                    for (const key in data) {
                        const taxonomy = data[key];
                        
                        // Create a label for the taxonomy filter.
                        this.toolbar.set(
                            `${taxonomy.slug}FilterLabel`,
                            new Label({
                                value: taxonomies.l10n.filterBy[taxonomy.slug],
                                attributes: {
                                    for: `media-attachment-${taxonomy.slugId}-filters`
                                },
                                priority: -72
                            }).render()
                        );

                        // Create the taxonomy filter.
                        this.toolbar.set(
                            `${taxonomy.slug}Filter`,
                            new taxonomyFilter({
                                controller: this.controller,
                                model: this.collection.props,
                                priority: -72,
                                queryVar: taxonomy.queryVar,
                                terms: taxonomy.terms,
                                id: `media-attachment-${taxonomy.slugId}-filters`,
                                allLabel: taxonomies.l10n.all[taxonomy.slug]
                            }).render()
                        );
                    }
                }
            });
        }(wpMedia.view.AttachmentsBrowser, wpMedia.view.AttachmentFilters.Taxonomy, wpMedia.view.Label, taxonomies);

        // Set up a change event handler for the taxonomy setting.
        $(document).on('change', '.setting[data-controls-attachment-taxonomy-setting] > select', function(event) {
            const options = [];
            // Gather selected options.
            for (const option of event.target.options) {
                if (option.selected) {
                    options.push(option.value);
                }
            }

            const $setting = $(event.target).parent();
            const targetSetting = $setting.attr('data-controls-attachment-taxonomy-setting');

            // Update the related input value and trigger a change event.
            $setting.parent().find(`.setting[data-setting=${targetSetting}] > input`).val(options.join(',')).trigger('change');
        });
    };
})();
