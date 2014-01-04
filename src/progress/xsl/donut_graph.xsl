<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:math="http://exslt.org/math"
    version="1.0">
    <xsl:template match="root">
        <xsl:variable name="margin" select="10"/>
        <xsl:variable name="donut_radius" select="25"/>
        
        <svg class="donut_chart" height="100%" width="100%" xmlns="http://www.w3.org/2000/svg">
            <xsl:for-each select="object">
                <xsl:variable name="preceding_count" select="count(preceding-sibling::object)"/>
                <xsl:variable name="cx" select="$margin + $donut_radius + $preceding_count*$margin + $preceding_count*$donut_radius*2"/>
                <xsl:variable name="cy" select="$margin + $donut_radius"/>
                <xsl:variable name="correct_endpoint_x" select="$cx - (math:cos( (math:constant('PI',10) div 2) + (2*math:constant('PI',10)*@ratio) )*$donut_radius)"/>
                <xsl:variable name="correct_endpoint_y" select="$cy - (math:sin( (math:constant('PI',10) div 2) + (2*math:constant('PI',10)*@ratio) )*$donut_radius)"/>
                
                <xsl:choose>
                    <xsl:when test="number(@ratio) = 0">
                        <path fill="red" d="M{$cx},{$cy - ($donut_radius div 2)}L{$cx},{$cy - $donut_radius}A{$donut_radius},{$donut_radius},0,0,1,{$cx},{$cy + $donut_radius}L{$cx},{$cy + ($donut_radius div 2)}A{$donut_radius div 2},{$donut_radius div 2},0,0,0,{$cx},{$cy - ($donut_radius div 2)}"></path>
                        <path fill="red" d="M{$cx},{$cy + ($donut_radius div 2)}L{$cx},{$cy + $donut_radius}A{$donut_radius},{$donut_radius},0,0,1,{$cx},{$cy - $donut_radius}L{$cx},{$cy - ($donut_radius div 2)}A{$donut_radius div 2},{$donut_radius div 2},0,0,0,{$cx},{$cy + ($donut_radius div 2)}"></path>
                    </xsl:when>
                    <xsl:when test="number(@ratio) = 1">
                        <path fill="green" d="M{$cx},{$cy - ($donut_radius div 2)}L{$cx},{$cy - $donut_radius}A{$donut_radius},{$donut_radius},0,0,1,{$cx},{$cy + $donut_radius}L{$cx},{$cy + ($donut_radius div 2)}A{$donut_radius div 2},{$donut_radius div 2},0,0,0,{$cx},{$cy - ($donut_radius div 2)}"></path>
                        <path fill="green" d="M{$cx},{$cy + ($donut_radius div 2)}L{$cx},{$cy + $donut_radius}A{$donut_radius},{$donut_radius},0,0,1,{$cx},{$cy - $donut_radius}L{$cx},{$cy - ($donut_radius div 2)}A{$donut_radius div 2},{$donut_radius div 2},0,0,0,{$cx},{$cy + ($donut_radius div 2)}"></path>                        
                    </xsl:when>
                    <xsl:when test="number(@ratio) &lt; .5">
                        <path fill="green" d="M{$cx},{$cy - ($donut_radius div 2)}L{$cx},{$cy - $donut_radius}A{$donut_radius},{$donut_radius},0,0,1,{$correct_endpoint_x},{$correct_endpoint_y}L{$cx + (($correct_endpoint_x - $cx) div 2)},{$cy + (($correct_endpoint_y - $cy) div 2)}A{$donut_radius div 2},{$donut_radius div 2},0,0,0,{$cx},{$cy - ($donut_radius div 2)}"></path>
                        <path fill="red" d="M{$cx + (($correct_endpoint_x - $cx) div 2)},{$cy + (($correct_endpoint_y - $cy) div 2)}L{$correct_endpoint_x},{$correct_endpoint_y}A{$donut_radius},{$donut_radius},0,1,1,{$cx},{$cy - $donut_radius}L{$cx},{$cy - ($donut_radius div 2)}A{$donut_radius div 2},{$donut_radius div 2},0,1,0,{$cx + (($correct_endpoint_x - $cx) div 2)},{$cy + (($correct_endpoint_y - $cy) div 2)}"></path>
                    </xsl:when>
                    <xsl:otherwise>
                        <path fill="green" d="M{$cx},{$cy - ($donut_radius div 2)}L{$cx},{$cy - $donut_radius}A{$donut_radius},{$donut_radius},0,1,1,{$correct_endpoint_x},{$correct_endpoint_y}L{$cx + (($correct_endpoint_x - $cx) div 2)},{$cy + (($correct_endpoint_y - $cy) div 2)}A{$donut_radius div 2},{$donut_radius div 2},0,1,0,{$cx},{$cy - ($donut_radius div 2)}"></path>
                        <path fill="red" d="M{$cx + (($correct_endpoint_x - $cx) div 2)},{$cy + (($correct_endpoint_y - $cy) div 2)}L{$correct_endpoint_x},{$correct_endpoint_y}A{$donut_radius},{$donut_radius},0,0,1,{$cx},{$cy - $donut_radius}L{$cx},{$cy - ($donut_radius div 2)}A{$donut_radius div 2},{$donut_radius div 2},0,0,0,{$cx + (($correct_endpoint_x - $cx) div 2)},{$cy + (($correct_endpoint_y - $cy) div 2)}"></path>
                    </xsl:otherwise>
                </xsl:choose>
            </xsl:for-each>
        </svg>
    </xsl:template>
</xsl:stylesheet>