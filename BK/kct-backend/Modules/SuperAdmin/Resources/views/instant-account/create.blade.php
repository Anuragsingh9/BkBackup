{{--This is for the first step of registration with Email --}}

@extends('superadmin::layouts.master')
@component('superadmin::components.auth_header')@endcomponent
@component('superadmin::components.navigation_bar')@endcomponent
@section('content')
    <div class="container page-content">
        {{-- Body content of page --}}
        <div class="col-xs-12 col-sm-12 pb-5">
            <h4 class="color1Txt mt-20 mb-30">
                <strong>{{ __('superadmin::labels.instant_account_creation') }}</strong>
            </h4>
            <h5>{{ __('superadmin::labels.enter_basic_for_instant_creation') }}</h5>
        </div>
        <div class="col-sm-12 col-sm-12">
            {{-- Form Heading Row --}}
            <div class="row">
                <div class="col-sm-2"><p>{{ __("superadmin::labels.org_fname") }}</p></div>
                <div class="col-sm-2"><p>{{ __("superadmin::labels.org_lname") }}</p></div>
                <div class="col-sm-2"><p>{{ __("superadmin::labels.org_email") }}</p></div>
                <div class="col-sm-2"><p>{{ __("superadmin::labels.org_name") }}</p></div>
                <div class="col-sm-2"><p>{{ __("superadmin::labels.account_name") }}</p></div>
            </div>
            <div class="row">
                <div class="col-lg-10">
                    <div class="errorBox">
                    </div>
                </div>
            </div>

            {{-- Form body --}}
            <div id="formList" class="row">

            </div>
            <div class="row">
                <div class="col-lg-2 mt-10">
                    <button id="btn-clone-form" style="background: none;border:none;padding: 0">
                        <i class="fa fa-plus-circle fa-3x site-color" aria-hidden="true"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function () {
            /**
             * ---------------------------------------------------------------------------------------------------------
             * @description to keep track of how many forms has been created till now
             * in starting there will be no form created so its 0 now
             * ---------------------------------------------------------------------------------------------------------
             *
             * @var
             */
            let currentFormCount = 0;

            /**
             * ---------------------------------------------------------------------------------------------------------
             * @description The prefix for the form id
             * form id prefix stored locally in method so it can be subtracted from submitted form to identify the num.
             * ---------------------------------------------------------------------------------------------------------
             */
            const formIdPre = 'accCreateForm';

            /**
             * ---------------------------------------------------------------------------------------------------------
             * @description To store the state for the local method
             * ---------------------------------------------------------------------------------------------------------
             */
            const state = {
                isApiProgressing: false,
                currentProcessingId: null,
            }


            // binding button to create a new form
            $("#btn-clone-form").click(function () {
                console.log("A form is demanded to create so preparing a new form");
                const formId = createForm();
                console.log("A form is created with id", formId);
            });

            /**
             * ---------------------------------------------------------------------------------------------------------
             * @description To create a form
             * increment to current form number so next form will have unique ids as well
             * prepare the ids for the form with new unique id by appending form number
             * append the html tags
             * ---------------------------------------------------------------------------------------------------------
             *
             * @return string
             */
            const createForm = function () {
                // increasing the count so next form can have a new unique id
                currentFormCount++;
                // the new id for the new form
                const formId = `${formIdPre + currentFormCount}`;
                // the new id for the form parent div as the id must different else all new form will append in first.
                const formParentDivId = `formRow${currentFormCount}`;
                // to create the necessary html tags
                appendFormHtmlTags(formId, formParentDivId);
                console.log("Current Form Count", currentFormCount);
                createFormHandler(formId);
                // if api is in progress then disable the new create button
                if (state.isApiProgressing) {
                    disableSubmitById(currentFormCount);
                }
                return formId;
            }

            /**
             * ---------------------------------------------------------------------------------------------------------
             * @description to assign the html tags to form
             * ---------------------------------------------------------------------------------------------------------
             *
             * @param formId
             * @param formParentDivId
             */
            const appendFormHtmlTags = function (formId, formParentDivId) {
                // reusable string
                const inputDiv = '<div class="form-group col-sm-2">';
                // creating a form parent div
                $("#formList").append(`<div class="row mb-10 formRowClass" id="${formParentDivId}">`);
                // form is created with new unique id
                $(`#${formParentDivId}`).append(`<form method="post" name="myForm" id="${formId}" class="form-inline row">{{ csrf_field() }}`);
                const formSelector = $(`#${formId}`);
                formSelector.append(`${inputDiv}<input id="${"orgFname" + currentFormCount}" type="text" value="" name="orgFname"/></div>`);
                formSelector.append(`${inputDiv}<input id="${"orgLname" + currentFormCount}" type="text" value="" name="orgLname"/></div>`);
                formSelector.append(`${inputDiv}<input id="${"orgEmail" + currentFormCount}" type="email" value="" name="orgEmail"/></div>`);
                formSelector.append(`${inputDiv}<input id="${"orgName" + currentFormCount}" type="text" value="" name="orgName"/></div>`);
                formSelector.append(`${inputDiv}<input id="${"accName" + currentFormCount}" type="text" value="" name="accName"/></div>`);
                formSelector.append(`<div class="form-group col-sm-2" id="${"createBtnDiv" + currentFormCount}"><input type="submit" id="${"formSubmit" + currentFormCount}" class="btn-primary" style="border-style: solid" value="Create"></div>`);
                formSelector.append(`<div class="form-group col-sm-2" id="${"accountLinkDiv" + currentFormCount}" style="display:none"><a href="#" target="_blank" id="${"accountLink" + currentFormCount}">Link</div>`);
            }

            /**
             * ---------------------------------------------------------------------------------------------------------
             * @description To create a form submit handler for resprective form number
             * ---------------------------------------------------------------------------------------------------------
             */
            const createFormHandler = function (formId) {
                $(`#${formId}`).validate({
                    rules: accCreateFormValidations,
                    messages: accCreateFormValMsg,
                    submitHandler: submitAccCreate
                });
            }

            /**
             * ---------------------------------------------------------------------------------------------------------
             * @description The actual validations to apply on the account create form
             * ---------------------------------------------------------------------------------------------------------
             *
             * @var
             */
            const accCreateFormValidations = {
                orgFname: {required: true,},
                orgLname: {required: true},
                orgEmail: {required: true, email: true},
                orgName: {required: true},
                accName: {required: true,},
            }

            /**
             * ---------------------------------------------------------------------------------------------------------
             * @description The validation messages to show when respective validation error is thrown
             * ---------------------------------------------------------------------------------------------------------
             *
             * @var
             */
            const accCreateFormValMsg = {
                orgFname: {required: "Please enter orgFname.",},
                orgLname: {required: "Please enter orgLname.",},
                orgEmail: {required: "Please enter orgEmail.", email: "Please enter a valid email.",},
                orgName: {required: "Please enter Org Name.",},
                accName: {required: "Please enter accName.",}
            };

            /**
             * ---------------------------------------------------------------------------------------------------------
             * @description Form method which is responsible for hitting the ajax call and sending data to api
             * This will hit the ajax call to bulk account store api
             * - First Add the headers to accept the json data from backend
             * - add the url
             * - add the post method
             * - prepare the data
             * - before sending convert the create (submit) button of form -> loading icon
             * - on complete check response and if success show the fqdn
             * - on failure handle the response accordingly
             * ---------------------------------------------------------------------------------------------------------
             *
             * @param form
             */
            const submitAccCreate = function (form) {
                const selectedId = parseInt(form.id.replace(formIdPre, ''));
                console.log("submitted form", selectedId);
                addHeaders();
                jQuery.ajax({
                    url: "{{ route('su-instant-acc-store') }}",
                    method: 'post',
                    data: getAccCreateFormData(selectedId),
                    beforeSend: function () {
                        beforeSubmit(selectedId)
                    },
                    complete: handleAccCreateRes,
                })
            }

            /**
             * ---------------------------------------------------------------------------------------------------------
             * @description To add the header to the form
             * This will add the accept json header so the request will always return the json response in case of any
             * response
             * ---------------------------------------------------------------------------------------------------------
             */
            const addHeaders = function () {
                $.ajaxSetup({headers: {"Accept": "application/json"}});
            }

            /**
             * ---------------------------------------------------------------------------------------------------------
             * @description to get the data to send for the form
             * ---------------------------------------------------------------------------------------------------------
             *
             * @return object
             */
            const getAccCreateFormData = function (selectedId) {
                return {
                    orgFname: jQuery(`#${"orgFname" + selectedId}`).val(),
                    orgLname: jQuery(`#${"orgLname" + selectedId}`).val(),
                    orgEmail: jQuery(`#${"orgEmail" + selectedId}`).val(),
                    orgName: jQuery(`#${"orgName" + selectedId}`).val(),
                    accName: jQuery(`#${"accName" + selectedId}`).val(),
                    _token: $("input[name=_token]").val(),
                }
            }

            /**
             * ---------------------------------------------------------------------------------------------------------
             * @description To disable the create button and convert it to loading button
             * When this method is called this will convert the create button of form to loading icon.
             * ---------------------------------------------------------------------------------------------------------
             */
            const beforeSubmit = function (selectedId) {
                // Show loader
                state.isApiProgressing = true;
                state.currentProcessingId = selectedId;
                $(`#${"formSubmit" + selectedId}`).val('Processing');
                disableAllSubmits(selectedId);
            };

            /**
             * ---------------------------------------------------------------------------------------------------------
             * @description to disable all the form submit buttons
             * ---------------------------------------------------------------------------------------------------------
             *
             * @param exclude // the id which need not to be converted
             */
            const disableAllSubmits = function (exclude) {
                for (let i = 1; i <= currentFormCount; i++) {
                    if (i === exclude) {
                        // if exclude then just disable the button only
                        const selector = $(`#${"formSubmit" + i}`);
                        selector.prop('disabled', true);
                    } else {
                        // else disable and change color also
                        disableSubmitById(i);
                    }
                }
            }

            /**
             * ---------------------------------------------------------------------------------------------------------
             * @description This will disable the single form button by id
             * ---------------------------------------------------------------------------------------------------------
             *
             * @param i // id to disable the button
             */
            const disableSubmitById = function (i) {
                const selector = $(`#${"formSubmit" + i}`);
                selector.prop('disabled', true);
                selector.removeClass('btn-primary').addClass('btn-secondary');
            }

            /**
             * ---------------------------------------------------------------------------------------------------------
             * @description To handle the response of the Account create api
             * First validate the response
             * if response is 200 and status is true
             *      show the fqdn accordingly
             * if the response is 422 i.e. validation error
             *      show the errors to respective place
             * if the response is 500 show it on the error section of page for internal server error.
             * ---------------------------------------------------------------------------------------------------------
             *
             * @param data
             */
            const handleAccCreateRes = function (data) {
                // to safely check if the request proceed successfully
                if (data && data.hasOwnProperty('responseJSON') && data.responseJSON.status === true && state.currentProcessingId !== null) {
                    $(`#${"createBtnDiv" + state.currentProcessingId}`).hide();
                    $(`#${"accountLinkDiv" + state.currentProcessingId}`).show();
                    const anchor = $(`#${"accountLink" + state.currentProcessingId}`);
                    anchor.attr("href", data.responseJSON.url);
                    anchor.text(data.responseJSON.fqdn);
                } else {
                    // if there is validation error displaying error
                    $(".errorBox").append(`<div class="alert alert-dismissible fade show alert-danger" id="errorSection">
                        <ul class="menu"></ul>
                        </div>`)
                    let errors = Object.values(data.responseJSON.errors);
                    errors.forEach(function(item) {
                        console.log(item);
                        $(".menu").append(`<li>${item}</li>`)
                    });
                    console.log("what failed", data, data.hasOwnProperty("responseJSON"), data.responseJSON);
                }
                enableAllButton();
            }

            /**
             * ---------------------------------------------------------------------------------------------------------
             * @description To enable all the submit buttons
             * ---------------------------------------------------------------------------------------------------------
             */
            const enableAllButton = function () {
                for (let i = 1; i <= currentFormCount; i++) {
                    enableButtonById(i);
                }
            }

            /**
             * ---------------------------------------------------------------------------------------------------------
             * @description To enable a button by id
             * change disabled to false
             * change class to primary button
             * change label to create
             * ---------------------------------------------------------------------------------------------------------
             *
             * @param i
             */
            const enableButtonById = function (i) {
                const selector = $(`#${"formSubmit" + i}`);
                selector.prop('disabled', false);
                selector.removeClass('btn-secondary').addClass('btn-primary');
                selector.val('Create');
            }

            // this will create a form for the first time so we can have one form
            createForm();

        });
    </script>

@endsection
