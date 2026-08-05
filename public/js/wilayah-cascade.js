(function () {
    function normalize(value) {
        return String(value || '').trim().toUpperCase();
    }

    function findOption(select, value) {
        if (!value) {
            return null;
        }

        const target = normalize(value);

        return Array.from(select.options).find(function (option) {
            return normalize(option.value) === target || normalize(option.textContent) === target;
        }) || null;
    }

    function ensureOption(select, value, label) {
        if (!value) {
            return null;
        }

        const existing = findOption(select, value);
        if (existing) {
            existing.selected = true;
            return existing;
        }

        const option = document.createElement('option');
        option.value = value;
        option.textContent = label || value;
        option.selected = true;
        option.dataset.fallback = '1';
        select.appendChild(option);
        return option;
    }

    function createFieldController(select, manualInput, panel, triggerBtn, hooks) {
        hooks = hooks || {};
        const cancelBtn = panel ? panel.querySelector('.wilayah-override-cancel') : null;

        function isOverride() {
            return select.dataset.overrideActive === '1';
        }

        function setTriggerEnabled(enabled) {
            if (triggerBtn) {
                triggerBtn.disabled = !enabled;
            }
        }

        function enterOverride(value, silent) {
            select.dataset.overrideActive = '1';
            select.required = false;
            select.disabled = true;
            select.classList.add('d-none');

            if (panel) {
                panel.classList.remove('d-none');
                panel.setAttribute('aria-hidden', 'false');
            }

            if (triggerBtn) {
                triggerBtn.classList.add('d-none');
            }

            if (manualInput) {
                manualInput.required = true;
                manualInput.value = value || '';
                if (!silent) {
                    window.setTimeout(function () {
                        manualInput.focus();
                    }, 100);
                }
            }

            if (!silent && typeof hooks.onEnter === 'function') {
                hooks.onEnter();
            }
        }

        function exitOverride(options) {
            options = options || {};
            if (typeof hooks.resolveExitOptions === 'function') {
                Object.assign(options, hooks.resolveExitOptions());
            }

            select.dataset.overrideActive = '0';
            select.required = true;
            select.classList.remove('d-none');

            if (options.reEnableSelect !== false) {
                select.disabled = false;
            }

            if (panel) {
                panel.classList.add('d-none');
                panel.setAttribute('aria-hidden', 'true');
            }

            if (triggerBtn) {
                triggerBtn.classList.remove('d-none');
            }

            if (manualInput) {
                manualInput.required = false;
                manualInput.value = '';
            }

            if (typeof hooks.onExit === 'function') {
                hooks.onExit(options);
            }
        }

        function applyForSubmit() {
            if (!isOverride() || !manualInput || !manualInput.value.trim()) {
                return;
            }

            select.disabled = false;
            ensureOption(select, manualInput.value.trim());
        }

        if (triggerBtn) {
            triggerBtn.addEventListener('click', function () {
                enterOverride('');
            });
        }

        if (cancelBtn) {
            cancelBtn.addEventListener('click', function () {
                exitOverride();
            });
        }

        return {
            isOverride: isOverride,
            enterOverride: enterOverride,
            exitOverride: exitOverride,
            applyForSubmit: applyForSubmit,
            setTriggerEnabled: setTriggerEnabled,
        };
    }

    window.initWilayahCascade = function (config) {
        const provinceSelect = document.getElementById(config.provinceId);
        const citySelect = document.getElementById(config.cityId);
        const districtSelect = document.getElementById(config.districtId);
        const initial = config.initial || {};

        if (!provinceSelect || !citySelect || !districtSelect) {
            return;
        }

        let provinceField;
        let cityField;
        let districtField;

        function resetCitySelect() {
            cityField.exitOverride({ reEnableSelect: false });
            citySelect.innerHTML = '<option value="">Pilih Kota/Kabupaten</option>';
            citySelect.disabled = true;
            cityField.setTriggerEnabled(false);
        }

        function resetDistrictSelect() {
            districtField.exitOverride({ reEnableSelect: false });
            districtSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';
            districtSelect.disabled = true;
            districtField.setTriggerEnabled(false);
        }

        function enableCityManualOnly() {
            cityField.exitOverride({ reEnableSelect: false });
            citySelect.innerHTML = '<option value="">Pilih Kota/Kabupaten</option>';
            citySelect.disabled = true;
            cityField.setTriggerEnabled(true);
        }

        function enableDistrictManualOnly() {
            districtField.exitOverride({ reEnableSelect: false });
            districtSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';
            districtSelect.disabled = true;
            districtField.setTriggerEnabled(true);
        }

        provinceField = createFieldController(
            provinceSelect,
            config.provinceManualId ? document.getElementById(config.provinceManualId) : null,
            config.provincePanelId ? document.getElementById(config.provincePanelId) : null,
            config.provinceTriggerId ? document.getElementById(config.provinceTriggerId) : null,
            {
                onEnter: function () {
                    initial.kota = '';
                    initial.kecamatan = '';
                    enableCityManualOnly();
                    resetDistrictSelect();
                },
                onExit: function () {
                    provinceSelect.value = '';
                    resetCitySelect();
                    resetDistrictSelect();
                },
            }
        );

        cityField = createFieldController(
            citySelect,
            config.cityManualId ? document.getElementById(config.cityManualId) : null,
            config.cityPanelId ? document.getElementById(config.cityPanelId) : null,
            config.cityTriggerId ? document.getElementById(config.cityTriggerId) : null,
            {
                onEnter: function () {
                    initial.kecamatan = '';
                    enableDistrictManualOnly();
                },
                onExit: function () {
                    resetDistrictSelect();
                },
                resolveExitOptions: function () {
                    return { reEnableSelect: !provinceField.isOverride() };
                },
            }
        );

        districtField = createFieldController(
            districtSelect,
            config.districtManualId ? document.getElementById(config.districtManualId) : null,
            config.districtPanelId ? document.getElementById(config.districtPanelId) : null,
            config.districtTriggerId ? document.getElementById(config.districtTriggerId) : null,
            {
                onExit: function () {
                    if (cityField.isOverride() || provinceField.isOverride()) {
                        districtSelect.disabled = true;
                    }
                },
                resolveExitOptions: function () {
                    return {
                        reEnableSelect: !cityField.isOverride() && !provinceField.isOverride(),
                    };
                },
            }
        );

        function restoreInitialDistrict() {
            if (!initial.kecamatan) {
                return;
            }

            const selectedDistrict = findOption(districtSelect, initial.kecamatan);
            if (selectedDistrict && !selectedDistrict.dataset.fallback) {
                selectedDistrict.selected = true;
                return;
            }

            districtField.enterOverride(initial.kecamatan, true);
        }

        function restoreInitialCity() {
            if (!initial.kota) {
                return;
            }

            const selectedCity = findOption(citySelect, initial.kota);
            if (selectedCity && selectedCity.dataset.cityId) {
                selectedCity.selected = true;
                loadDistricts(selectedCity.dataset.cityId);
                return;
            }

            cityField.enterOverride(initial.kota, true);
            enableDistrictManualOnly();
            restoreInitialDistrict();
        }

        function loadProvinces() {
            provinceField.setTriggerEnabled(true);

            fetch(config.routes.provinces)
                .then(function (response) { return response.json(); })
                .then(function (provinces) {
                    provinceSelect.innerHTML = '<option value="">Pilih Provinsi</option>';
                    provinces.forEach(function (province) {
                        const option = document.createElement('option');
                        option.value = province.name;
                        option.textContent = province.name;
                        option.dataset.provinceId = province.id;
                        provinceSelect.appendChild(option);
                    });

                    if (!initial.provinsi) {
                        return;
                    }

                    const selectedProvince = findOption(provinceSelect, initial.provinsi);
                    if (selectedProvince && selectedProvince.dataset.provinceId) {
                        selectedProvince.selected = true;
                        loadCities(selectedProvince.dataset.provinceId);
                        return;
                    }

                    provinceField.enterOverride(initial.provinsi, true);
                    enableCityManualOnly();
                    restoreInitialCity();
                })
                .catch(function (error) {
                    console.error('Error loading provinces:', error);
                    provinceSelect.innerHTML = '<option value="">Gagal memuat provinsi</option>';
                    provinceField.setTriggerEnabled(true);
                    if (initial.provinsi) {
                        provinceField.enterOverride(initial.provinsi, true);
                        enableCityManualOnly();
                        restoreInitialCity();
                    }
                });
        }

        function loadCities(provinceId) {
            cityField.exitOverride({ reEnableSelect: false });
            citySelect.disabled = true;
            citySelect.innerHTML = '<option value="">Memuat kota/kabupaten...</option>';
            cityField.setTriggerEnabled(false);
            resetDistrictSelect();

            fetch(config.routes.cities + '?province_id=' + encodeURIComponent(provinceId))
                .then(function (response) { return response.json(); })
                .then(function (cities) {
                    citySelect.innerHTML = '<option value="">Pilih Kota/Kabupaten</option>';
                    cities.forEach(function (city) {
                        const option = document.createElement('option');
                        option.value = city.name;
                        option.textContent = city.name;
                        option.dataset.cityId = city.id;
                        citySelect.appendChild(option);
                    });
                    citySelect.disabled = false;
                    cityField.setTriggerEnabled(true);
                    restoreInitialCity();
                })
                .catch(function (error) {
                    console.error('Error loading cities:', error);
                    citySelect.innerHTML = '<option value="">Gagal memuat kota/kabupaten</option>';
                    cityField.setTriggerEnabled(true);
                    if (initial.kota) {
                        cityField.enterOverride(initial.kota, true);
                        enableDistrictManualOnly();
                        restoreInitialDistrict();
                    }
                });
        }

        function loadDistricts(regencyId) {
            districtField.exitOverride({ reEnableSelect: false });
            districtSelect.disabled = true;
            districtSelect.innerHTML = '<option value="">Memuat kecamatan...</option>';
            districtField.setTriggerEnabled(false);

            fetch(config.routes.districts + '?regency_id=' + encodeURIComponent(regencyId))
                .then(function (response) { return response.json(); })
                .then(function (districts) {
                    districtSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';
                    districts.forEach(function (district) {
                        const option = document.createElement('option');
                        option.value = district.name;
                        option.textContent = district.name;
                        districtSelect.appendChild(option);
                    });
                    districtSelect.disabled = false;
                    districtField.setTriggerEnabled(true);
                    restoreInitialDistrict();
                })
                .catch(function (error) {
                    console.error('Error loading districts:', error);
                    districtSelect.innerHTML = '<option value="">Gagal memuat kecamatan</option>';
                    districtField.setTriggerEnabled(true);
                    if (initial.kecamatan) {
                        districtField.enterOverride(initial.kecamatan, true);
                    }
                });
        }

        provinceSelect.addEventListener('change', function () {
            if (provinceField.isOverride()) {
                return;
            }

            initial.kota = '';
            initial.kecamatan = '';

            const selected = this.options[this.selectedIndex];
            const provinceId = selected ? selected.dataset.provinceId : '';
            if (provinceId) {
                loadCities(provinceId);
            } else {
                resetCitySelect();
                resetDistrictSelect();
            }
        });

        citySelect.addEventListener('change', function () {
            if (cityField.isOverride()) {
                return;
            }

            initial.kecamatan = '';

            const selectedCity = findOption(citySelect, this.value);
            if (selectedCity && selectedCity.dataset.cityId) {
                loadDistricts(selectedCity.dataset.cityId);
            } else {
                resetDistrictSelect();
            }
        });

        const form = provinceSelect.closest('form');
        if (form) {
            form.addEventListener('submit', function () {
                provinceField.applyForSubmit();
                cityField.applyForSubmit();
                districtField.applyForSubmit();
            });
        }

        loadProvinces();
    };
})();
