<?php use_helper('Number') ?>
<?php include_partial('dashboard/admin_tabs', array('active' => 'product'));
$subscription = StoreTable::value(StoreTable::BILLING_SUBSCRIPTION_ENABLE); ?>
<table class="table table-bordered table-striped">
  <thead>
      <tr>
        <th class="span3">Name</th>
        <th>Price (net)</th>
        <th>Credits (participants, emails)</th>
        <th>Duration (days)</th>
        <?php if ($subscription): ?><th>Subscription / Abo</th><?php endif ?>
        <th class="span2"></th>
      </tr>
  </thead>
  <tbody>
    <?php foreach ($list as $product): /* @var $product Product */ ?>
    <tr>
      <td><?php echo $product->getName() ?></td>
      <td><?php echo format_currency($product->getPrice(), StoreTable::value(StoreTable::BILLING_CURRENCY)) ?></td>
      <td><?php echo format_number($product->getEmails()) ?></td>
      <td><?php echo format_number($product->getDays()) ?></td>
      <?php if ($subscription): ?><td><?php echo $product->getSubscription() ? 'yes' : 'no' ?></td><?php endif ?>
      <td>
          <a class="btn btn-mini" href="<?php echo url_for('product_edit', array('id' => $product->getId())) ?>">edit</a>
          <a class="btn btn-mini ajax_link" href="<?php echo url_for('product_delete', array('id' => $product->getId())) ?>">delete</a>
      </td>
    </tr>
    <?php endforeach ?>
  </tbody>
</table>
<a class="btn" href="<?php echo url_for('product_new') ?>">Create new product</a>
