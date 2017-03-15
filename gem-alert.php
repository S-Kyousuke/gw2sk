<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css">
    <link rel="stylesheet" href="style.css?v=1.1.0">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
    <script src="time-utils.js?v=1.0.0"></script>
    <script src="db-functions.js?v=1.0.0"></script>
    <script src="gw2-sk.js?v=1.1.0"></script>
    <script src="view-utils.js?v=1.1.0"></script>
    <script src="item-box.js?v=1.0.0"></script>
    <script>
        $(document).ready(function() {

            var gemField = $('#gemField');
            var gemAmount = $('#gemAmount');
            var goldAmount = $('#goldAmount');
            var silverAmount = $('#silverAmount');
            var copperAmount = $('#copperAmount');

            var dotText = $('<span>....</span>');
            var gemCost = dotText;

            var updateTask = null;
            var updateButton = $('#updateButton');
            var updateLabel = $('#updateLabel');
            var timeCountLabel = $('#timeCount');
            var autoUpdateCheckBox = $('#autoUpdate');
            var fastModeCheckBox = $('#fastMode');

            var alertBox = $('#alertBox');
            var alertSwitch = $('#alertSwitch');
            var alertMessage = $('#alertMessage');

            var targetCostsBoxValue = 0;
            var targetCosts = (function() {
                var value = 0;
                var listener = null;
                return {
                    setListener: function(newListener) {
                        listener = newListener;
                    },
                    set: function(newValue) {
                        value = newValue;
                        if (listener != null) {
                            listener(newValue);
                        }
                    },
                    get: function() {
                        return value;
                    }
                };
            })();

            var currentCosts = (function() {
                var value = 0;
                var listener = null;
                return {
                    setListener: function(newListener) {
                        listener = newListener;
                    },
                    set: function(newValue) {
                        value = newValue;
                        if (listener != null) {
                            listener(newValue);
                        }
                    },
                    get: function() {
                        return value;
                    }
                };
            })();

            var ringSound = new Audio("sounds/ring.mp3");
            ringSound.loop = true;

            var enableAlertButton = function() {
                alertBox.removeClass('disabled');
                alertSwitch.bootstrapToggle('enable');
            }

            var disableAlertButton = function() {
                alertBox.addClass('disabled');
                alertSwitch.bootstrapToggle('disable');
            }

            var disableTargetInput = function() {
                goldAmount.attr('disabled', '');
                silverAmount.attr('disabled', '');
                copperAmount.attr('disabled', '');
            };

            var enableTargetInput = function() {
                goldAmount.removeAttr('disabled');
                silverAmount.removeAttr('disabled');
                copperAmount.removeAttr('disabled');
            };

            var playRingSound = function() {
                ringSound.play();
            };

            var stopRingSound = function() {
                ringSound.pause();
                ringSound.currentTime = 0;
            };

            targetCosts.setListener(function(newValue) {
                if (newValue >= currentCosts.get()) {
                    playRingSound();
                } else {
                    stopRingSound();
                }
            });

            currentCosts.setListener(function(newValue) {
                if (newValue <= targetCosts.get()) {
                    playRingSound();
                } else {
                    stopRingSound();
                }
            });

            updateLabel.hide();

            $("#costs").append(gemCost);

            var delayTask = (function() {
                var timerId = 0;
                return function(callback, ms) {
                    clearTimeout(timerId);
                    timerId = setTimeout(callback, ms);
                };
            })();

            // find gem cost function
            var findGemCost = function() {
                updateLabel.hide();

                gemField.attr('disabled', '');
                autoUpdateCheckBox.attr('disabled', '');
                fastModeCheckBox.attr('disabled', '');
                updateButton.attr('disabled', '');

                var gemAmountValue = gemAmount.val();
                if (gemAmountValue != '' && Gw2Sk.isInt(gemAmountValue) && gemAmountValue > 0) {
                    gemCost.replaceWith(dotText);
                    gemCost = dotText;

                    var update = function(price) {
                        currentCosts.set(price);
                        var newGemCost = $(ViewUtils.getCoinValueDivTag(price, false));
                        gemCost.replaceWith(newGemCost);
                        gemCost = newGemCost;
                        gemField.removeAttr('disabled');
                        autoUpdateCheckBox.removeAttr('disabled', '');
                        fastModeCheckBox.removeAttr('disabled', '');
                        updateButton.removeAttr('disabled', '');

                        if (autoUpdateCheckBox.is(":checked")) {
                            startAutoUpdate();
                        }
                    };

                    if (fastModeCheckBox.is(":checked")) {
                        Gw2Sk.getGemPrice(gemAmountValue, update);
                    } else {
                        Gw2Sk.getExactGemPrice(gemAmountValue, update);
                    }

                } else {
                    currentCosts.set(0);
                    gemCost.replaceWith(dotText);
                    gemCost = dotText;
                    gemField.removeAttr('disabled');
                    autoUpdateCheckBox.removeAttr('disabled');
                    fastModeCheckBox.removeAttr('disabled');
                    updateButton.removeAttr('disabled');
                }
            }

            // input listener
            gemAmount.on('input', function() {
                delayTask(findGemCost, 1000);
            });

            //disable form summit with Enter key      
            $("form").bind("keypress", function(e) {
                if (e.keyCode == 13) {
                    $("#btnSearch").attr('value');
                    e.preventDefault();
                }
            });

            findGemCost();

            var startAutoUpdate = function() {
                clearInterval(updateTask);
                updateLabel.show();
                const UPDATE_INTERVAL_SEC = 180;
                timeCountLabel.text(UPDATE_INTERVAL_SEC);
                updateTask = Gw2Sk.intervalTask(1000, UPDATE_INTERVAL_SEC, function(callCount) {
                    timeCountLabel.text((UPDATE_INTERVAL_SEC - callCount));
                    if (callCount == UPDATE_INTERVAL_SEC) {
                        findGemCost();
                    }
                });
            }

            var stopAutoUpdate = function() {
                clearInterval(updateTask);
                updateLabel.hide();
            }

            autoUpdateCheckBox.change(function() {
                if (autoUpdateCheckBox.is(":checked")) {
                    startAutoUpdate();
                } else {
                    stopAutoUpdate();
                }
            });

            updateButton.on('click', findGemCost);

            var onTargetChange = function() {
                var goldValue = goldAmount.val();
                var silverValue = silverAmount.val();
                var copperValue = copperAmount.val();

                var validGold = Gw2Sk.isInt(goldValue) && goldValue >= 0;
                var validSilver = Gw2Sk.isInt(silverValue) && silverValue >= 0 && silverValue <= 99;
                var validCopper = Gw2Sk.isInt(copperValue) && copperValue >= 0 && copperValue <= 99;

                if (!validGold || !validSilver || !validCopper) {
                    disableAlertButton();
                    targetCostsBoxValue = 0;
                } else {
                    enableAlertButton();
                    targetCostsBoxValue = parseInt(goldValue) * 10000 + parseInt(silverValue) * 100 + parseInt(copperValue);
                }
            };

            goldAmount.on('input', onTargetChange);
            silverAmount.on('input', onTargetChange);
            copperAmount.on('input', onTargetChange);

            alertSwitch.change(function() {
                if (alertSwitch.prop('checked')) {
                    targetCosts.set(targetCostsBoxValue);
                    disableTargetInput();
                } else {
                    targetCosts.set(0);
                    enableTargetInput();
                }
            });
        });
    </script>
</head>

<body>
    <nav class="navbar navbar-inverse">
        <div class="container-fluid">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <div class="navbar-header"><a class="navbar-brand" href="javascript:void(0)">GW2SK</a>
            </div>
            <div class="collapse navbar-collapse" id="navbar">
                <ul class="nav navbar-nav">
                    <li><a href="bag">Bag Income</a>
                    </li>
                    <li><a href="material">Material Price</a>
                    </li>
                    <li><a href="average">Avgerage Price</a>
                    </li>
                    <li class="active"><a href="gem-alert">Gem Alert</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container">
        <form>
            <div class="row zero-margin">
                <h3>Coins to Gems</h3>
            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-4 col-md-3 col-lg-2">
                    <fieldset id="gemField">
                        <div class="form-group form-group--gem-alert has-feedback">
                            <input type="number" class="form-control" id="gemAmount" value="100" placeholder="Enter Gem">
                            <i class="icon-gem form-control-feedback"></i>
                        </div>
                    </fieldset>
                </div>
                <div class="col-xs-12 col-sm-8 col-md-9 col-lg-10">
                    <fieldset class="form-inline">
                        <div class="form-group">
                            <label class="control-label control-label--gem-alert">Costs</label>
                            <div id="costs" class="form-control-static"></div>
                        </div>
                    </fieldset>
                </div>
            </div>
            <div class="row row--checkbox">
                <fieldset class="form-inline">
                    <div class="form-group middle-vert">
                        <label class="checkbox-inline">
                            <input type="checkbox" id="autoUpdate" value="" checked>Auto update
                        </label>
                        <label class="checkbox-inline">
                            <input type="checkbox" id="fastMode" value="" checked>Fast Mode
                        </label>
                    </div>
                </fieldset>
            </div>
            <div class="row zero-margin">
                <div class="form-control-static">
                    <button type="button" class="btn btn-default" id="updateButton">Update</button>
                    <span id="updateLabel" class="update-label">Next update in <span id="timeCount"></span> seconds.</span>
                </div>
            </div>
        </form>
        <form>
            <h4>Target Costs</h4>
            <div class="row row--coin-input">
                <div class="col-xs-4 col-sm-3 col-md-3 col-lg-2 coin-input">
                    <div class="form-group has-feedback">
                        <input type="text" class="form-control" id="goldAmount" value="">
                        <i class="icon-gold-coin form-control-feedback"></i>
                    </div>
                </div>
                <div class="col-xs-4 col-sm-2 col-md-2 col-lg-1 coin-input">
                    <div class="form-group has-feedback">
                        <input type="text" maxlength="2" class="form-control" id="silverAmount" value="">
                        <i class="icon-silver-coin form-control-feedback"></i>
                    </div>
                </div>
                <div class="col-xs-4 col-sm-2 col-md-2 col-lg-1 coin-input">
                    <div class="form-group has-feedback">
                        <input type="text" maxlength="2" class="form-control" id="copperAmount" value="">
                        <i class="icon-copper-coin form-control-feedback"></i>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12">
                    <div id="alertBox" class="checkbox zero-margin disabled">
                        <input id="alertSwitch" type="checkbox" data-toggle="toggle" data-on="Alert On" data-off="Alert Off" disabled>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12">
                    <span id="alertLabel" class="alert-label"></span>
                </div>
            </div>
        </form>
    </div>
</body>

</html>