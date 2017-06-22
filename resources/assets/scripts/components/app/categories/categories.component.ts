import { Component, Inject, OnInit } from '@angular/core';
import { Router } from '@angular/router';

import { Category } from '../../../class/category';
import { CategoryService } from '../../../services/category.service';

@Component({
    'selector': 'state-template',
    'template': require('./categories.template.html')
})
export class CategoriesComponent implements OnInit {

	title = 'Categories';

	/**
     * @type {Router}
     */
    private router: Router;

	/**
     * @type {Category}
     */
    categories: Category[];

	constructor (
        @Inject(Router) router: Router,
		private categoryService: CategoryService
    ) {
        this.router = router;
	}

	getCategories() {
		this.categoryService
			.getCategories()
	  		.then(categories => this.categories = categories);
	}

	ngOnInit(): void {
		this.getCategories();
	}

	public run (): void {}



}