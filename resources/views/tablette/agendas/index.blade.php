@extends('tablette.app')

@section('content')

<div class="col-md-12">

	{!! $calendar !!}

	@if (!empty($produits))
		<div class="clearfix">
			<div class="col-md-9">
				<h3>Liste des ingrédients nécessaire de la semaine</h3>
			</div>
			<div class="col-md-2 text-right marginTop15">
				<a class="btn btn-sm btn-danger" href="/listes-courses/generate" onclick="return(confirm('La génération de la liste écrasera la liste courante. Etes-vous sûr?'));">
					<span class="glyphicon glyphicon-shopping-cart"></span>
					Générer la liste de course
				</a>
			</div>
		</div>

		<table class="table table-bordered table-hover">
			<thead>
				<tr>
					<th>Nom</th>
					<th>Quantité nécessaire</th>
					<th>Quantité en stock</th>
					<th>Quantité manquante</th>
				</tr>
			</thead>
			<tbody>
				@foreach ($produits as $produit)
					<tr>
						<td>{{ $produit->produit_nom }}</td>
						<td>{{ $produit->necessaire }} {{ !is_null($produit->unite) ? $produit->unite : '' }}</td>
						<td>{{ $produit->en_stock }} {{ !is_null($produit->unite) ? $produit->unite : '' }}</td>
						<td>
							@if ($produit->manquant > 0)
								{{ $produit->manquant }} {{ !is_null($produit->unite) ? $produit->unite : '' }}
							@else
								---
							@endif
						</td>
					</tr>
				@endforeach
			</tbody>
		</table>
	@endif

</div>

@endsection