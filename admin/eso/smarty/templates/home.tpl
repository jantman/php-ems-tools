{* +----------------------------------------------------------------------+ *}
{* | MPAC/PHP EMS Tools Finance Component - http://www.php-ems-tools.com  | *}
{* +----------------------------------------------------------------------+ *}
{* | Copyright (c) 2006, 2007 Jason Antman.                               | *}
{* |                                                                      | *}
{* | This program is free software; you can redistribute it and/or modify | *}
{* | it under the terms of the GNU General Public License as published by | *}
{* | the Free Software Foundation; either version 3 of the License, or    | *}
{* | (at your option) any later version.                                  | *}
{* |                                                                      | *}
{* | This program is distributed in the hope that it will be useful,      | *}
{* | but WITHOUT ANY WARRANTY; without even the implied warranty of       | *}
{* | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the        | *}
{* | GNU General Public License for more details.                         | *}
{* |                                                                      | *}
{* | You should have received a copy of the GNU General Public License    | *}
{* | along with this program; if not, write to:                           | *}
{* |                                                                      | *}
{* | Free Software Foundation, Inc.                                       | *}
{* | 59 Temple Place - Suite 330                                          | *}
{* | Boston, MA 02111-1307, USA.                                          | *}
{* +----------------------------------------------------------------------+ *}
{* |Please use the above URL for bug reports and feature/support requests.| *}
{* +----------------------------------------------------------------------+ *}
{* | Authors: Jason Antman <jason@jasonantman.com>                        | *}
{* +----------------------------------------------------------------------+ *}
{* | $LastChangedRevision:: 7                                           $ | *}
{* | $HeadURL:: http://svn.jasonantman.com/mpac-finance/smarty/template#$ | *}
{* +----------------------------------------------------------------------+ *}

<div style="float: left; width: 40%; margin-right: 5%; margin-left: 5%;">
<p style="text-align: left;"><strong>Accounts</strong></p>
<table class="minorTable">
<tr>
<th>name</th><th>type</th><th>balance</th><th>matures on</th>
</tr>
{foreach item=account from=$accounts}
   <tr>
      <td><a href="ledger.php?type=account&id={$account.id}">{$account.name}</a></td>
      <td>{$account.type}</td>
      <td>{$account.balance|moneycolor}</td>
      <td>{$account.matures_on|date_format:$config.date}</td>
   </tr>
{/foreach}
<tr><td><strong>Total</strong></td><td>&nbsp;</td><td>{$acct_total|moneycolor}</td><td>&nbsp;</td></tr>
</table>
</div>

<div style="float: right; width: 40%; margin-right: 5%; margin-left: 5%;">
<p style="text-align: left;"><strong>Budgets</strong></p>
<table class="minorTable">
<tr>
<th>name</th><th>balance</th>
</tr>
{foreach item=budget from=$budgets}
   <tr>
      {if $budget.parent != NULL}
      <td>&nbsp;&nbsp;&nbsp;&nbsp;<a href="ledger.php?type=budget&id={$budget.id}">{$budget.name}</a></td>
      {else}
      <td><a href="ledger.php?type=budget&id={$budget.id}">{$budget.name}</a></td>
      {/if}
      <td>{$budget.balance|moneycolor}</td>
   </tr>
{/foreach}
<tr><td><strong>Total</strong></td><td>{$budg_total|moneycolor}</td></tr>
</table>
</div>
<div class="clearing">&nbsp;</div>
