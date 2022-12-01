<?php

namespace Modules\Cocktail\Transformers;

use Illuminate\Http\Resources\Json\Resource;

class WPArticlesResource extends Resource {
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request) {
        return [
            'image'       => isset($this['image']) ? $this['image'] : '',
            'date'        => isset($this['date']) ? $this['date'] : '',
            'title'       => isset($this['title']) ? $this['title'] : '',
            'description' => isset($this['description']) ? $this['description'] : '',
            'post_url'    => isset($this['post_url']) ? $this['post_url'] : '',
        ];
    }
}

/*
 *
"id": 1554,
"title": "ABV +_Présentation du projet",
"description": "<p>Cette seconde démarche, baptisée Atelier BIM virtuel Plus (ABV+) a été lancée en septembre 2017. Elle visait une montée en puissance de l’expérimentation, en reprenant les données de l’immeuble de 30 logements sociaux à La Rochelle déjà rétro conçu en BIM.</p>\n<p>L’objectif cherchait à préciser et à détailler ce que peut apporter concrètement la mise en œuvre du BIM dans quatre domaines particuliers à la démarche constructive :</p>\n<ul>\n<li>L’élaboration du cahier des charges BIM par le maître d’ouvrage,</li>\n<li>L’approche en BIM du coût global d’un projet,</li>\n<li>L’analyse du cycle de vie (ACV) d’une construction à partir d’une maquette BIM,</li>\n<li>L’organisation des chantiers avec l’utilisation de la 4D, et d’en diffuser les enseignements.</li>\n</ul>\n<p>ABV+ réunit durant près de six mois, 21 organisations professionnelles et 55 entreprises, soit 132 acteurs. Associés aux quatre ateliers, chacun était néanmoins centré sur une problématique précise.</p>\n",
"post_url": "https://projectdevzone.com/planbim2022/actions/plan-bim-2022-ptnb-axe-a/abv/",
"image": "https://projectdevzone.com/planbim2022/wp-content/uploads/2020/02/plan-bim-2022-abvplus-logo-585x362.jpg",
"date": "01-02-2020"
 */