
/**
 * @type {Object}
 * @property {String} field Color key name
 * @property {Object} value Value
 * @property {Number} value.r 'r' value
 * @property {Number} value.g 'g' value
 * @property {Number} value.b 'b' value
 * @property {Number} value.a 'a' value
 * @example {
    field: "key name",
    value:{
        r: 5,
        g: 5,
        b: 51,
        a: 0
    },
}
 */
let GraphicsData_colorObj = {
    field: "key name",
    value:{
        r: 5,
        g: 5,
        b: 51,
        a: 0
    },
}
/**
 * @type {Object}
 * @property {String} field Color key name
 * @property {String} value value
 * @example {
    "field": "apply_customisation",
    "value": 1
  }
 */
let GraphicsData_valueObj = {
    "field": "apply_customisation",
    "value": 1
  }


/**
 * @type {GraphicsData_valueObj[]}
 */
let graphicsArray = [
    {
      "field": "main_color_1",
      "value": {
        "r": 136,
        "g": 119,
        "b": 119,
        "a": 1
      }
    },
    {
      "field": "main_color_2",
      "value": {
        "r": 13,
        "g": 184,
        "b": 243,
        "a": 1
      }
    },
    {
      "field": "header_bg_color_1",
      "value": {
        "r": 255,
        "g": 255,
        "b": 255,
        "a": 1
      }
    },
    {
      "field": "header_separation_line_color",
      "value": {
        "r": 231,
        "g": 231,
        "b": 231,
        "a": 1
      }
    },
    {
      "field": "header_text_color",
      "value": {
        "r": 184,
        "g": 233,
        "b": 134,
        "a": 1
      }
    },
    {
      "field": "customized_join_button_bg",
      "value": {
        "r": 65,
        "g": 144,
        "b": 182,
        "a": 1
      }
    },
    {
      "field": "customized_join_button_text",
      "value": {
        "r": 255,
        "g": 255,
        "b": 255,
        "a": 1
      }
    },
    {
      "field": "sh_background",
      "value": {
        "r": 59,
        "g": 59,
        "b": 59,
        "a": 1
      }
    },
    {
      "field": "conv_background",
      "value": {
        "r": 184,
        "g": 233,
        "b": 134,
        "a": 1
      }
    },
    {
      "field": "badge_background",
      "value": {
        "r": 255,
        "g": 255,
        "b": 255,
        "a": 1
      }
    },
    {
      "field": "space_background",
      "value": {
        "r": 21,
        "g": 255,
        "b": 47,
        "a": 0.75
      }
    },
    {
      "field": "user_grid_background",
      "value": {
        "r": 59,
        "g": 59,
        "b": 59,
        "a": 1
      }
    },
    {
      "field": "user_grid_pagination_color",
      "value": {
        "r": 5,
        "g": 137,
        "b": 184,
        "a": 1
      }
    },
    {
      "field": "event_tag_color",
      "value": {
        "r": 5,
        "g": 137,
        "b": 184,
        "a": 1
      }
    },
    {
      "field": "professional_tag_color",
      "value": {
        "r": 109,
        "g": 53,
        "b": 173,
        "a": 1
      }
    },
    {
      "field": "personal_tag_color",
      "value": {
        "r": 53,
        "g": 173,
        "b": 129,
        "a": 1
      }
    },
    {
      "field": "tags_text_color",
      "value": {
        "r": 255,
        "g": 255,
        "b": 255,
        "a": 1
      }
    },
    {
      "field": "content_background",
      "value": {
        "r": 59,
        "g": 59,
        "b": 59,
        "a": 1
      }
    },
    {
      "field": "apply_customisation",
      "value": 1
    },
    {
      "field": "header_footer_customized",
      "value": 0
    },
    {
      "field": "button_customized",
      "value": 0
    },
    {
      "field": "texture_customized",
      "value": 0
    },
    {
      "field": "texture_square_corners",
      "value": 0
    },
    {
      "field": "texture_remove_frame",
      "value": 0
    },
    {
      "field": "texture_remove_shadows",
      "value": 0
    },
    {
      "field": "sh_customized",
      "value": 0
    },
    {
      "field": "sh_hide_on_off",
      "value": 0
    },
    {
      "field": "conv_customization",
      "value": 0
    },
    {
      "field": "badge_customization",
      "value": 0
    },
    {
      "field": "space_customization",
      "value": 0
    },
    {
      "field": "unselected_spaces_square",
      "value": 0
    },
    {
      "field": "selected_spaces_square",
      "value": 0
    },
    {
      "field": "extends_color_user_guide",
      "value": 1
    },
    {
      "field": "user_grid_customization",
      "value": 0
    },
    {
      "field": "tags_customization",
      "value": 0
    },
    {
      "field": "content_customized",
      "value": 0
    },
    {
      "field": "label_customized",
      "value": 0
    },
    {
      "field": "general_setting",
      "value": 0
    },
    {
      "field": "invite_attendee",
      "value": 0
    },
    {
      "field": "video_explainer",
      "value": 1
    },
    {
      "field": "group_has_own_customization",
      "value": 1
    },
    {
      "field": "header_line_1",
      "value": null
    },
    {
      "field": "header_line_2",
      "value": null
    },
    {
      "field": "qss_video_url",
      "value": "https://www.youtube.com/watch?v=VnyitUU4DUY"
    },
    {
      "field": "event_image",
      "value": "https://s3.eu-west-2.amazonaws.com/kct-dev/general/event_image/default.jpg",
      "is_default": 1
    },
    {
      "field": "group_logo",
      "value": "https://s3.eu-west-2.amazonaws.com/kct-dev/general/group_logo/default.png",
      "is_default": 1
    },
    {
      "field": "business_team_icon",
      "value": null
    },
    {
      "field": "business_team_altImage",
      "value": null
    },
    {
      "field": "vip_icon",
      "value": null
    },
    {
      "field": "vip_altImage",
      "value": null
    },
    {
      "field": "moderator_icon",
      "value": null
    },
    {
      "field": "expert_icon",
      "value": null
    },
    {
      "field": "expert_altImage",
      "value": null
    },
    {
      "field": "video_explainer_alternative_image",
      "value": "https://s3.eu-west-2.amazonaws.com/kct-dev/assets/video_explainer_alt/9poF3SlBKDABonydYXnabSwfIxeCREpk5pbQSJyL.png",
      "is_default": 1
    }
  ]